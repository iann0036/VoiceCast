<?php
    $img_url = urldecode($_REQUEST['i']);

    $im = imagecreatefromgif($img_url);
    if(!$im)
        die('Failed to load');

    imagefilter($im, IMG_FILTER_NEGATE);
    //imagefilter($im, IMG_FILTER_SMOOTH, 1);
    //imagealphablending($im, false);
    //imagesavealpha($im, true);
    //$white = imagecolorexact($im, 255, 255, 255);
    //imagecolortransparent($im, $white);

    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);