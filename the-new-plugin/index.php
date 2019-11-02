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
$tempPath952 = plugin_dir_path( __FILE__ );
$a = strpos($tempPath952,"/wp-content/");
$tempPath952 = substr($tempPath952,$a);
?>

<?php

////////////////////begin functions for upload callback////////////////////
function aw_scripts() {
    // Register the script
    global $tempPath952;
    wp_register_script( 'aw-custom', $tempPath952.'js/customUpload.js', array('jquery'), '1.1', true );
 
    // Localize the script with new data
    $script_data_array = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'pluginAbsolutePath'=>$tempPath952
    );
    wp_localize_script( 'aw-custom', 'aw', $script_data_array );
 
    // Enqueued script with localized data.
    wp_enqueue_script( 'aw-custom' );
}

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
 
add_action( 'wp_enqueue_scripts', 'aw_scripts' );
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