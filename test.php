<?php

header ("Content-type: image/jpeg");

$string = "Содержание вредных веществ в реках Башкирии превышено почти в 50 раз";


$font = 25;

$fontArial = 'cuprum.ttf';

$width = imagefontwidth($font) * strlen($string) ;

$height = imagefontheight($font) ;

$im = imagecreatefromjpeg("news.jpg");

$x = imagesx($im) - $width ;

$y = imagesy($im) - $height;

$backgroundColor = imagecolorallocate ($im, 255, 255, 255);

$textColor = imagecolorallocate ($im, 0, 0,0);


$widthString = 430;
$arr = explode(' ', $string);
$res = '';

foreach ($arr as $word) {

    $tmpString = $res . ' ' . $word;

    $textBox = imagettfbbox($font, 0, $fontArial, $tmpString);

    if($textBox[2]>$widthString) {
        $res.=($res==""?"":"\n").$word;
    }
    else {
        $res.=($res==""?"":" ").$word;
    }
}


//imagestring ($im, $font, $x, $y, $string, $textColor);

imagettftext($im, $font, 0, 50, 110, $textColor, $fontArial, $res);

imagejpeg($im);

?>
