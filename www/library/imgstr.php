<?php
$data = base64_decode($HTTP_GET_VARS['dt']);
$im = imagecreate(65,20);
$white = imagecolorallocate($im,255,255,255);
$gray = imagecolorallocate($im, 210,210,210);
$black = imagecolorallocate($im, 0,0,0);
$blue = imagecolorallocate($im, 0,0,255);
imagestring($im,5,8,2,$data,$blue);
imageline($im,0,2,65,2,$gray);
//imageline($im,0,10,65,10,$gray);
imageline($im,0,17,65,17,$gray);
imagepng($im);
?>