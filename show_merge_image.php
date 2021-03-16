<?php

//find and repalce
$background = imagecreatefrompng('images/blankimage.png');
$insert = imagecreatefromjpeg($_GET['vImg']);
imagecolortransparent($insert, imagecolorat($insert, 0, 0));
$insert_x = imagesx($insert);
$insert_y = imagesy($insert);


imagecopymerge($background, $insert, 99, 58, 0, 0, $insert_x, $insert_y, 100);
header('Content-type: image/jpg');
imagegif($background);
?>