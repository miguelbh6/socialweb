<?php
if (!session_id()) session_start();

$input = array("a","b","c","d","e","0","1","2","3","4","5","6","7","8","9");
$rand_keys = array_rand($input,5);

$codigo = $input[$rand_keys[0]].$input[$rand_keys[1]].$input[$rand_keys[2]].$input[$rand_keys[3]].$input[$rand_keys[4]];

header("Content-type:image/gif");
$img = imagecreate(80,30);
$preto = imagecolorallocate($img,0,0,0);
$branco = imagecolorallocate($img,255,255,255);
imagettftext($img,15,10,10,28,$branco,"font/verdana.ttf","$codigo");
imagegif($img);
imagedestroy($img);

$_SESSION["codigocaptcha"] = $codigo ;
?>