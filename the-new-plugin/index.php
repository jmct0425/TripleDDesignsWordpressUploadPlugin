<?php
/**
 * Plugin Name: 1 Test Plugin
 * Plugin URI: http://www.starnesconsulting.com/demo-plugin
 * Description: Boilerplate template
 * Version: 1.0
 * Author: Justin McTaggart
 * Author URI: http://www.starnesconsulting.com
 */
///this includes the options file
date_default_timezone_set('America/New_York');
defined( 'ABSPATH' ) || exit;
include( plugin_dir_path( __FILE__ ) . 'options.php');
$tempPath = plugin_dir_path( __FILE__ );
$tempurl = admin_url( 'admin-ajax.php' );
?>

<script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
<script>
	
	jQuery(function($) {
		jQuery('body').on('change', '.fileInputElement', function() {
			console.log('file change noticed....');
			var dataId = jQuery(this).data('id');
			$this = jQuery(this);
			file_obj = $this.prop('files');
			form_data = new FormData();
			for(i=0; i<file_obj.length; i++) {
				var temp = jQuery('#linkArray'+dataId).val();
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
            var linkArray = jQuery('#linkArray'+dataId).val(temp+'|'+file_obj[i].name);
            form_data.append('file[]', file_obj[i]);
        }
        form_data.append('action', 'file_upload');
        console.log('beginning ajax call for file upload...');
        var tempUrl = "<?php echo $tempurl;?>";
        jQuery.ajax({
        	url: tempUrl,
        	type: 'POST',
        	contentType: false,
        	processData: false,
        	beforeSend: function(){
        		console.log('displaying loading gif....');
        		jQuery('#uploadFileSelect'+dataId).fadeOut(400);
        		jQuery('#uploadLoadingGif'+dataId).fadeIn(400);
        	},
        	data: form_data,
        	success: function (response) {
                //$this.val('');
                console.log('successfully upload..');
                //jQuery('#uploadLoadingGif'+dataId).fadeOut(400);
                //jQuery('#uploadFileSelect'+dataId).fadeIn(400);
                //jQuery('#linkArray'+dataId).fadeIn(400);
                var temp = jQuery('#linkArray'+dataId).val();
                var newTemp = temp.substr(1);
                jQuery('#linkArray'+dataId).val(newTemp);
                jQuery('#linkArrayLabel'+dataId).html(newTemp);
                //clear the imageContainer
                jQuery('#imagePreview'+dataId).html('');
                console.log('beginning element creation loop...');
                jQuery.each( file_obj, function( key, value ) {
                	var tempName = file_obj.name;
                	var tempMonthNumber = jQuery('#monthNumber'+dataId).val();
                	var tempYear= jQuery('#year'+dataId).val();
                	var imagePath = "wp-content/uploads/"+tempYear+"/"+tempMonthNumber+"/";
                    //update the image preview containter
                    var createImageElement = "<img id='imagePreview"+dataId+"' class='imagePreviewElement' src='http://www.tripleddesigns.com/"+imagePath+file_obj[key].name+"' style='max-height:300px;width:150px;'>";
                    console.log('creating image element...'+createImageElement);
                    jQuery('#imagePreview'+dataId).append(createImageElement); 
                });
                jQuery('#save'+dataId).fadeIn(400,function(){
                	console.log('save button clicked...');
                	var dataId = jQuery(this).data('id');
                	var tempKey = jQuery('#key'+dataId).val();
                	var tempProductId = jQuery('#productId'+dataId).val();
                	var tempName = jQuery('#name'+dataId).val();
                	var tempToday = jQuery('#today'+dataId).val();
                	var tempMonthNumber = jQuery('#monthNumber'+dataId).val();
                	var tempYear= jQuery('#year'+dataId).val();
                	var imagePath = "wp-content/uploads/"+tempYear+"/"+tempMonthNumber+"/";
                	var nameArr = jQuery('#linkArray'+dataId).val();
                	console.log('ajaxing the mail module...');
                    var tempPath = "<?php echo $tempPath;?>";
                	jQuery.ajax({
                		url: '/notifyAdminForUploads.php',
                		type: 'POST',
                		beforeSend: function(){
                			console.log('displaying loading gif....');
                            console.log(nameArr);
                			jQuery('#save'+dataId).css('color','green');
                		},
                		data: {tempKey:tempKey,tempProductId:tempProductId,tempName:tempName,tempToday:tempToday,tempMonthNumber:tempMonthNumber,tempYear:tempYear,imagePath:imagePath,nameArray:nameArr},
                		success: function (thedataresponse) {
                			console.log(thedataresponse);
                			jQuery('#save'+dataId).fadeOut(400);
                			console.log('successfully mailed..');
                			jQuery('#uploadLoadingGif'+dataId).fadeOut(400);
                			jQuery('#uploadFileSelect'+dataId).fadeIn(400);
                			jQuery('#uploadButton'+dataId).fadeOut(400);
                		}
                	});
                });

            }
        });
    });
});
</script>
<?php

////////////////////begin functions for upload callback////////////////////

function file_upload_callback() {
    $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');
 
    for($i = 0; $i < count($_FILES['file']['name']); $i++) {
 
        if (in_array($_FILES['file']['type'][$i], $arr_img_ext)) {
 
            wp_upload_bits($_FILES['file']['name'][$i], null, file_get_contents($_FILES['file']['tmp_name'][$i]));
        }
    }
    echo "File(s) uploaded successfully";
    wp_die();
}
 
add_action( 'wp_ajax_file_upload', 'file_upload_callback' );
add_action( 'wp_ajax_nopriv_file_upload', 'file_upload_callback' );
////////////////////end upload callback function section//////////////////////////////////////

$counter = 0;

function create_upload_section_in_cart_item ($content) {
	//echo '<pre>';
	//var_dump($content);
	//echo '</pre>';
//create condition to check for specific tag to add images to items-
global $counter;
$cart_item=$content;
$tempKey = $cart_item['key'];
$product_id = $cart_item['product_id'];
$tempProductId = $cart_item['product_id'];
$tempName = $cart_item['data']->name;
$tempToday = date("m-d-Y");
$tempMonthNumber = date("m");
$tempYear= date("Y");
$imagePath = "wp-content/uploads/".$tempYear.'/'.$tempMonthNumber.'/';
for($x=1;$x<=$cart_item['quantity'];$x++){
	$terms = get_the_terms($product_id,'product_tag');
	if($terms[0]->name=='needs_file_upload' AND strtolower($_SERVER['REQUEST_URI'])!='/cart/'){
//		if($terms[0]->name=='needs_file_upload'){
		?>
				<form class="fileUpload" enctype="multipart/form-data">
					<div id="uploadFileSelect<?php echo $counter+$x;?>" class="form-group fileSelect">
						<label>Choose Files for <?php echo $tempName.' #'.$x?>:</label>
						<br><small>[This is a multi select option, please make sure you choose all images at once]</small>
						<input id="uploadButton<?php echo $counter+$x;?>" class="fileInputElement" name="file" data-id="<?php echo $counter+$x;?>" type="file" accept="image/*" multiple />
						<input id="key<?php echo $counter+$x;?>" name="key<?php echo $counter+$x;?>" class="hidden" value="<?php echo $tempKey;?>">
						<input id="productId<?php echo $counter+$x;?>" name="productId<?php echo $counter+$x;?>" class="hidden" value="<?php echo $productId;?>">
						<input id="name<?php echo $counter+$x;?>" name="name<?php echo $counter+$x;?>" class="hidden" value="<?php echo $tempName;?>">
						<input id="today<?php echo $counter+$x;?>" name="today<?php echo $counter+$x;?>" class="hidden" value="<?php echo $tempToday;?>">
						<input id="monthNumber<?php echo $counter+$x;?>" name="monthNumber<?php echo $counter+$x;?>" class="hidden" value="<?php echo $tempMonthNumber;?>">
						<input id="year<?php echo $counter+$x;?>" name="year<?php echo $counter+$x;?>" class="hidden" value="<?php echo $tempYear;?>">
						<input id="imagePath<?php echo $counter+$x;?>" name="imagePath<?php echo $counter+$x;?>" class="hidden" value="<?php echo $imagePath;?>">
						<br><label>Filenames: </label><span id="linkArrayLabel<?php echo $counter+$x;?>"></span><input id="linkArray<?php echo $counter+$x;?>" class="linkArray" type="text" value="" style="display:none;width:100%;">
						<div id="imagePreview<?php echo $counter+$x;?>"></div>
					</div>
					<img id="uploadLoadingGif<?php echo $counter+$x;?>" class="uploadLoadingGif" data-id="item-<?php echo $counter+$x;?>" src="/img/loading.gif" style="width:200px;display:none;">			    
					<button id="save<?php echo $counter+$x;?>" type="button" data-id="<?php echo $counter+$x;?>" class="btn btn-primary savePictures" style="display:none;">SAVEING...</button>								    
				</form>
		<?
	}

}

$counter++;

//echo $content = '<p>Thank you for reading!</p>';
}

///this is the actural functionality of the plugin
//add_action( 'woocommerce_review_order_after_cart_contents', 'my_thank_you_text', 10 );
//add_action( 'woocommerce_review_order_after_cart_contents', 'my_thank_you_text' );

//turn me on to activate	
add_action( 'woocommerce_after_cart_item_name', 'create_upload_section_in_cart_item' );

?>