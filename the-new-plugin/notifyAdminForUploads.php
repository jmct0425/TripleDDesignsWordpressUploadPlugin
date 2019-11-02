<?php
	$to = 'contact@tripleddesigns.com, jmct0425@gmail.com';  //contact@tripleddesigns.com
	$subject = 'Uploads item:'.$_POST['tempName'].' #'.$_POST['tempKey'];
	//{tempKey:tempKey,tempProductId:tempProductId,tempName:tempName,tempToday:tempToday,tempMonthNumber:tempMonthNumber,tempYear:tempYear,imagePath:imagePath,imageNames:imageNames,nameArray:nameArr}
	$body = "<html><head><title>New Update</title></head><body>You have received new image uploads on your website:<br>";
	$body .="<p><ul><li><b>Key: #".$_POST['tempKey']."</b></li><li>#".$_POST['tempProductId']."<i>".$_POST['tempName']."</i></li>
	<li><b>Date:</b>".$_POST['tempToday']."</li>";
	$temp = explode("|",$_POST['nameArray']);
	foreach($temp as $item){
		$counter=1;
		$itemExt = strtolower(substr($item,-3)); 
		$item=substr($item,0,(strlen($item)-3));
		$item = $item.$itemExt;
		$body .= "<li>Image #".$counter.": <a href='http://www.tripleddesigns.com/".$_POST['imagePath'].$item."'>".$_POST['imagePath'].$item."</a></li>";
		$counter++;
	}
	$body .= "</ul></p></body></html>";
	// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <contact@tripleddesigns.com>' . "\r\n";
	 
	mail( $to, $subject, $body, $headers );

?>