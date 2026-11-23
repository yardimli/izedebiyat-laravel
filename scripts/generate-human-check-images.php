<?php

// Offline asset builder only. Run locally and commit the resulting PNGs.
// No HTTP route or runtime service calls this script.
$root = dirname(__DIR__);
$paragraphs = require $root.'/resources/human-check/paragraphs.php';
$font = $root.'/public/assets/fonts/NotoSans-Regular.ttf';
$output = $root.'/public/human-check-images';

if (! extension_loaded('gd') || ! function_exists('imagettftext')) {
    throw new RuntimeException('Generating assets locally requires GD with FreeType.');
}
if (! is_dir($output)) {
    mkdir($output, 0755, true);
}
foreach ($paragraphs as $number => $lines) {
    $image = imagecreatetruecolor(1000, 380);
    $paper = imagecolorallocate($image, 251, 248, 240);
    $ink = imagecolorallocate($image, 36, 56, 49);
    $muted = imagecolorallocate($image, 131, 143, 132);
    imagefill($image, 0, 0, $paper);
    foreach ($lines as $index => $line) {
        $bounds = imagettfbbox(20, 0, $font, $line);
        if (max($bounds[0], $bounds[2], $bounds[4], $bounds[6]) > 910) {
            throw new RuntimeException('Paragraph '.($number + 1).' has a line too wide for the image.');
        }
        $y = 55 + $index * 46;
        imagettftext($image, 15, 0, 24, $y, $muted, $font, (string) ($index + 1));
        imagettftext($image, 20, 0, 62, $y, $ink, $font, $line);
    }
    $path = sprintf('%s/paragraph-%02d.png', $output, $number + 1);
    imagepng($image, $path, 9);
    imagedestroy($image);
    echo basename($path).PHP_EOL;
}
