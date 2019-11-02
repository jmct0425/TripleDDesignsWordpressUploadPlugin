jQuery(function($) {
    $('body').on('change', '.fileInputElement', function() {
        console.log('file change noticed....');
        var dataId = $(this).data('id');
        $this = $(this);
        file_obj = $this.prop('files');
        form_data = new FormData();
        for(i=0; i<file_obj.length; i++) {
            var temp = $('#linkArray'+dataId).val();
            //lowercase the uploaded filename extension
            var currentFilename = file_obj[i].name;
            var currentFilenameLength = currentFilename.length;
            var currentFilenameSansExt = currentFilename.substring(0, currentFilenameLength-3);
            var currentFilenameExtension = currentFilename.substring(currentFilenameLength-3);
            var newFilenameExtension = currentFilenameExtension.toLowerCase();
            console.log('currentFilenameExtension...:'+currentFilenameExtension);
            console.log('newFilenameExtension...:'+newFilenameExtension);
            console.log('newFileName...'+currentFilenameSansExt+newFilenameExtension);
            file_obj[i].name = currentFilenameSansExt+newFilenameExtension;            
            console.log(file_obj[i]);
            var linkArray = $('#linkArray'+dataId).val(temp+'|'+file_obj[i].name);
            form_data.append('file[]', file_obj[i]);
        }
        form_data.append('action', 'file_upload');
        console.log('beginning ajax call for file upload...');
        $.ajax({
            url: aw.ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            beforeSend: function(){
                console.log('displaying loading gif....');
                $('#uploadFileSelect'+dataId).fadeOut(400);
                $('#uploadLoadingGif'+dataId).fadeIn(400);
            },
            data: form_data,
            success: function (response) {
                //$this.val('');
                console.log('successfully upload..');
                //$('#uploadLoadingGif'+dataId).fadeOut(400);
                //$('#uploadFileSelect'+dataId).fadeIn(400);
                //$('#linkArray'+dataId).fadeIn(400);
                var temp = $('#linkArray'+dataId).val();
                var newTemp = temp.substr(1);
                $('#linkArray'+dataId).val(newTemp);
                $('#linkArrayLabel'+dataId).html(newTemp);
                //clear the imageContainer
                $('#imagePreview'+dataId).html('');
                console.log('beginning element creation loop...');
                $.each( file_obj, function( key, value ) {
                    var tempName = file_obj.name;
                    var tempMonthNumber = $('#monthNumber'+dataId).val();
                    var tempYear= $('#year'+dataId).val();
                    var imagePath = "wp-content/uploads/"+tempYear+"/"+tempMonthNumber+"/";
                    //update the image preview containter
                    var createImageElement = "<img id='imagePreview"+dataId+"' class='imagePreviewElement' src='http://www.tripleddesigns.com/"+imagePath+file_obj[key].name+"' style='max-height:300px;width:150px;'>";
                    console.log('creating image element...'+createImageElement);
                    $('#imagePreview'+dataId).append(createImageElement); 
                });
                $('#save'+dataId).fadeIn(400,function(){
                            console.log('save button clicked...');
                                var dataId = $(this).data('id');
                                        var tempKey = $('#key'+dataId).val();
                                        var tempProductId = $('#productId'+dataId).val();
                                        var tempName = $('#name'+dataId).val();
                                        var tempToday = $('#today'+dataId).val();
                                        var tempMonthNumber = $('#monthNumber'+dataId).val();
                                        var tempYear= $('#year'+dataId).val();
                                        var imagePath = "wp-content/uploads/"+tempYear+"/"+tempMonthNumber+"/";
                                        var nameArr = $('#linkArray'+dataId).val();
                                        console.log('ajaxing the mail module...');
                             $.ajax({
                                url: aw.pluginAbsolutePath+'notifyAdminForUploads.php',
                                type: 'POST',
                                beforeSend: function(){
                                    console.log('displaying loading gif....');
                                    $('#save'+dataId).css('color','green');
                                },
                                data: {tempKey:tempKey,tempProductId:tempProductId,tempName:tempName,tempToday:tempToday,tempMonthNumber:tempMonthNumber,tempYear:tempYear,imagePath:imagePath,nameArray:nameArr},
                                success: function (response) {
                                    console.log(response);
                                    $('#save'+dataId).fadeOut(400);
                                    console.log('successfully mailed..');
                                    $('#uploadLoadingGif'+dataId).fadeOut(400);
                                    $('#uploadFileSelect'+dataId).fadeIn(400);
                                    $('#uploadButton'+dataId).fadeOut(400);
                                }
                            });
                });

            }
        });
    });
});