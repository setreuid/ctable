<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// echo "ERROR";
// exit(1);

header( "Access-Control-Allow-Origin: *" );
require_once( "db/conn.php" );

/*
 *------------------------------------------------------------------------------
 * Editor Pick 업로드된 이미지 뿌리기
 *------------------------------------------------------------------------------
 *
 * @param  idx {Number}
 * @return     {IMAGE}
 *
 */

$conn = getDBConnectionWithMysqli();
// DB 접속

$sql_select_image = "Select data From BOARD_IMAGE Where id = " . $_REQUEST["idx"];

$res = $conn->query( $sql_select_image );

if ( $item = $res->fetch_object() )
{
    $max_width = 400;
    Header("Content-type:image/jpeg");

    // Get original size of image
    $image = imagecreatefromstring($item->data);
    $current_width = imagesx($image);
    $current_height = imagesy($image);

    // Set thumbnail width
    $widths = array($current_width, $max_width);
    $new_width = min($widths);

    // Calculate thumbnail height from given width to maintain ratio
    $new_height = $current_height / $current_width*$new_width;

    // Create new image using thumbnail sizes
    $thumb = imagecreatetruecolor($new_width,$new_height);

    // Copy original image to thumbnail
    fastimagecopyresampled($thumb,$image,0,0,0,0,$new_width,$new_height,$current_width,$current_height);

    // Show thumbnail on screen
    $show = imagejpeg($thumb);

    // Clean memory
    imagedestroy($image);
    imagedestroy($thumb);
}
else
{
    echo "ERROR";
}

mysqli_close( $conn );
// DB 종료

function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
    // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
    // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
    // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
    // Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
    //
    // Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
    // Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
    // 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
    // 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
    // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
    // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
    // 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.

    if (empty($src_image) || empty($dst_image) || $quality <= 0) { return false; }
    if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
        $temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
        imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
        imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
        imagedestroy ($temp);
    } else imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    return true;
}

?>
