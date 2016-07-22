<?php

/*
// +--------------------------------------------------------------------------+
// | Project:    TBDevYSE - TBDev Yuna Scatari Edition                        |
// +--------------------------------------------------------------------------+
// | This file is part of TBDevYSE. TBDevYSE is based on TBDev,               |
// | originally by RedBeard of TorrentBits, extensively modified by           |
// | Gartenzwerg.                                                             |
// |                                                                          |
// | TBDevYSE is free software; you can redistribute it and/or modify         |
// | it under the terms of the GNU General Public License as published by     |
// | the Free Software Foundation; either version 2 of the License, or        |
// | (at your option) any later version.                                      |
// |                                                                          |
// | TBDevYSE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with TBDevYSE; if not, write to the Free Software Foundation,      |
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            |
// +--------------------------------------------------------------------------+
// |                                               Do not remove above lines! |
// +--------------------------------------------------------------------------+
*/

require_once("include/bittorrent.php");
require_once("include/captcha.php");
dbconn(false);

$img_width = 201;
$img_height = 61;

// The following settings are only used for TTF fonts
$min_size = 20;
$max_size = 32;

$min_angle = -30;
$max_angle = 30;

if($_GET['imagehash'] == "test" || strlen($_GET['imagehash']) != 32) {
	$imagestring = "Yuna";
} else {
	$query = sql_query("SELECT * FROM captcha WHERE imagehash=".sqlesc($_GET['imagehash'])." LIMIT 1");
	if (!$query)
		die('Something bad hapened...');
	$regimage = mysql_fetch_array($query);
	$imagestring = $regimage['imagestring'];
	if (!$imagestring)
		$imagestring = 'ERROR';
}

$ttf_fonts = array();

// We have support for true-type fonts (FreeType 2)
if(function_exists("imagefttext")) {
	// Get a list of the files in the 'catpcha_fonts' directory
	$ttfdir  = @opendir("include/captcha_fonts");
	if($ttfdir) {
		while($file = readdir($ttfdir)) {
			// If this file is a ttf file, add it to the list
			if(is_file("include/captcha_fonts/".$file) && get_extension($file) == "ttf") {
				$ttf_fonts[] = "include/captcha_fonts/".$file;
			}
		}
	}
}

// Have one or more TTF fonts in our array, we can use TTF captha's
if(count($ttf_fonts) > 0) {
	$use_ttf = 1;
} else {
	$use_ttf = 0;
}

// Get backgrounds
$backgrounds = array();
if ($handle = @opendir('include/captcha_backs/')) {
	while ($filename = readdir($handle)) {
		if (preg_match('#\.(gif|jpg|jpeg|jpe|png)$#i', $filename)) {
			$backgrounds[] =  "include/captcha_backs/$filename";
		}
	}
	closedir($handle);
}

$notdone = true;

while ($notdone AND !empty($backgrounds)) {
	$index = mt_rand(0, count($backgrounds) - 1);
	$background = $backgrounds["$index"];
	switch(strtolower(file_extension($background))) {
		case 'jpg':
		case 'jpe':
		case 'jpeg':
			if (!function_exists('imagecreatefromjpeg') OR !$im = imagecreatefromjpeg($background)) {
				unset($backgrounds["$index"]);
			} else {
				$notdone = false;
			}
			break;
		case 'gif':
			if (!function_exists('imagecreatefromgif') OR !$im = imagecreatefromgif($background)) {
				unset($backgrounds["$index"]);
			} else {
				$notdone = false;
			}
			break;
		case 'png':
			if (!function_exists('imagecreatefrompng') OR !$im = imagecreatefrompng($background)) {
				unset($backgrounds["$index"]);
			} else {
				$notdone = false;
			}
			break;
	}
	sort($backgrounds);
}

if (TIMENOW & 2 && function_exists('imagerotate'))
	$im = imagerotate($im, 180, 0);

// Check for GD >= 2, create base image
/*if(gd_version() >= 2) {
	$im = imagecreatetruecolor($img_width, $img_height);
} else {
	$im = imagecreate($img_width, $img_height);
}*/

// No GD support, die.
if(!$im) {
	die("No GD support.");
}

// Fill the background with white
$bg_color = imagecolorallocate($im, 255, 255, 255);
imagefill($im, 0, 0, $bg_color);

// Draw random circles, squares or lines?
$to_draw = rand(0, 2);
if($to_draw == 1) {
	draw_circles($im);
} else if($to_draw == 2) {
	draw_squares($im);
} else {
	draw_lines($im);
}

// Draw dots on the image
draw_dots($im);

// Write the image string to the image
draw_string($im, $imagestring);

// Draw a nice border around the image
$border_color = imagecolorallocate($im, 0, 0, 0);
imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);

// Output the image
header("Content-type: image/png");
header('Cache-control: max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', (TIMENOW + 31536000)) . ' GMT');
header('Content-disposition: inline; filename=' . $imageinfo['filename']);
header('Content-transfer-encoding: binary');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', TIMENOW) . ' GMT');
header('ETag: "' . TIMENOW . '-' . session_id() . '"');
imagepng($im);
imagedestroy($im);
exit;

function file_extension($filename) {
	return substr(strrchr($filename, '.'), 1);
}

/**
 * Draws a random number of lines on the image.
 *
 * @param resource The image.
 */
function draw_lines(&$im) {
	global $img_width, $img_height;

	for($i = 10; $i < $img_width; $i += 10) {
		$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
		imageline($im, $i, 0, $i, $img_height, $color);
	}
	for($i = 10; $i < $img_height; $i += 10) {
		$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
		imageline($im, 0, $i, $img_width, $i, $color);
	}
}

/**
 * Draws a random number of circles on the image.
 *
 * @param resource The image.
 */
function draw_circles(&$im) {
	global $img_width, $img_height;
	
	$circles = $img_width*$img_height / 100;
	for($i = 0; $i <= $circles; $i++) {
		$color = imagecolorallocate($im, rand(180, 255), rand(180, 255), rand(180, 255));
		$pos_x = rand(1, $img_width);
		$pos_y = rand(1, $img_height);
		$circ_width = ceil(rand(1, $img_width)/2);
		$circ_height = rand(1, $img_height);
		imagearc($im, $pos_x, $pos_y, $circ_width, $circ_height, 0, rand(200, 360), $color);
	}
}

/**
 * Draws a random number of dots on the image.
 *
 * @param resource The image.
 */
function draw_dots(&$im) {
	global $img_width, $img_height;
	
	$dot_count = $img_width*$img_height/5;
	for($i = 0; $i <= $dot_count; $i++) {
		$color = imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255));
		imagesetpixel($im, rand(0, $img_width), rand(0, $img_height), $color);
	}	
}

/**
 * Draws a random number of squares on the image.
 *
 * @param resource The image.
 */
function draw_squares(&$im)
{
	global $img_width, $img_height;
	
	$square_count = 30;
	for($i = 0; $i <= $square_count; $i++) {
		$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
		$pos_x = rand(1, $img_width);
		$pos_y = rand(1, $img_height);
		$sq_width = $sq_height = rand(10, 20);
		$pos_x2 = $pos_x + $sq_height;
		$pos_y2 = $pos_y + $sq_width;
		imagefilledrectangle($im, $pos_x, $pos_y, $pos_x2, $pos_y2, $color); 
	}
}

/**
 * Writes text to the image.
 *
 * @param resource The image.
 * @param string The string to be written
 */
function draw_string(&$im, $string) {
	global $use_ttf, $min_size, $max_size, $min_angle, $max_angle, $ttf_fonts, $img_height, $img_width;
	
	$spacing = $img_width / my_strlen($string);
	$string_length = my_strlen($string);
	for($i = 0; $i < $string_length; $i++) {
		// Using TTF fonts
		if($use_ttf) {
			// Select a random font size
			$font_size = rand($min_size, $max_size);
			
			// Select a random font
			$font = array_rand($ttf_fonts);
			$font = $ttf_fonts[$font];
	
			// Select a random rotation
			$rotation = rand($min_angle, $max_angle);
			
			// Set the colour
			$r = rand(0, 200);
			$g = rand(0, 200);
			$b = rand(0, 200);
			$color = imagecolorallocate($im, $r, $g, $b);
			
			// Fetch the dimensions of the character being added
			$dimensions = imageftbbox($font_size, $rotation, $font, $string[$i], array());
			$string_width = $dimensions[2] - $dimensions[0];
			$string_height = $dimensions[3] - $dimensions[5];

			// Calculate character offsets
			//$pos_x = $pos_x + $string_width + ($string_width/4);
			$pos_x = $spacing / 4 + $i * $spacing;
			$pos_y = ceil(($img_height-$string_height/2));
			
			if($pos_x + $string_width > $img_width) {
				$pos_x = $pos_x - ($pos_x - $string_width);
			}

			// Draw a shadow
			$shadow_x = rand(-3, 3) + $pos_x;
			$shadow_y = rand(-3, 3) + $pos_y;
			$shadow_color = imagecolorallocate($im, $r+20, $g+20, $b+20);
			imagefttext($im, $font_size, $rotation, $shadow_x, $shadow_y, $shadow_color, $font, $string[$i], array());
			
			// Write the character to the image
			imagefttext($im, $font_size, $rotation, $pos_x, $pos_y, $color, $font, $string[$i], array());
		} else {
			// Get width/height of the character
			$string_width = imagefontwidth(5);
			$string_height = imagefontheight(5);

			// Calculate character offsets
			$pos_x = $spacing / 4 + $i * $spacing;
			$pos_y = $img_height / 2 - $string_height -10 + rand(-3, 3);
			
			// Create a temporary image for this character
			if(gd_version() >= 2) {
				$temp_im = imagecreatetruecolor(15, 20);
			} else {
				$temp_im = imagecreate(15, 20);
			}
			$bg_color = imagecolorallocate($temp_im, 255, 255, 255);
			imagefill($temp_im, 0, 0, $bg_color);
			imagecolortransparent($temp_im, $bg_color);

			// Set the colour
			$r = rand(0, 200);
			$g = rand(0, 200);
			$b = rand(0, 200);
			$color = imagecolorallocate($temp_im, $r, $g, $b);
			
			// Draw a shadow
			$shadow_x = rand(-1, 1);
			$shadow_y = rand(-1, 1);
			$shadow_color = imagecolorallocate($temp_im, $r+50, $g+50, $b+50);
			imagestring($temp_im, 5, 1+$shadow_x, 1+$shadow_y, $string[$i], $shadow_color);
			
			imagestring($temp_im, 5, 1, 1, $string[$i], $color);
			
			// Copy to main image
			imagecopyresized($im, $temp_im, $pos_x, $pos_y, 0, 0, 40, 55, 15, 20);
			imagedestroy($temp_im);
		}
	}
}

/**
 * Obtain the version of GD installed.
 *
 * @return float Version of GD
 */
function gd_version() {
	static $gd_version;
	
	if($gd_version) {
		return $gd_version;
	}
	if(!extension_loaded('gd')) {
		return;
	}
	
	ob_start();
	phpinfo(8);
	$info = ob_get_contents();
	ob_end_clean();
	$info = stristr($info, 'gd version');
	preg_match('/\d/', $info, $gd);
	$gd_version = $gd[0];
	
	return $gd_version;
}
?>