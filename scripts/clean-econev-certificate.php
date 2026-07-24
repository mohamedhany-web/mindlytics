<?php

$src = __DIR__ . '/../public/certificates/templates/econev-achievement.png';
$dstBlank = __DIR__ . '/../public/certificates/templates/econev-achievement-blank.png';
$dstJpeg = __DIR__ . '/../public/certificates/templates/econev-achievement.jpg';

// File is actually JPEG despite .png extension
$im = @imagecreatefromjpeg($src);
if (!$im) {
    $im = @imagecreatefrompng($src);
}
if (!$im) {
    fwrite(STDERR, "Failed to load image\n");
    exit(1);
}

imagesavealpha($im, true);
$w = imagesx($im);
$h = imagesy($im);

$sampleX = (int) ($w * 0.50);
$sampleY = (int) ($h * 0.28);
$rgb = imagecolorat($im, $sampleX, $sampleY);
$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
$fill = imagecolorallocate($im, $r, $g, $b);

echo "size={$w}x{$h} fill={$r},{$g},{$b}\n";

$paint = function (float $xPct, float $yPct, float $wPct, float $hPct) use ($im, $w, $h, $fill) {
    $x1 = (int) round($w * $xPct / 100);
    $y1 = (int) round($h * $yPct / 100);
    $x2 = (int) round($w * ($xPct + $wPct) / 100) - 1;
    $y2 = (int) round($h * ($yPct + $hPct) / 100) - 1;
    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $fill);
};

// Logo mark + text
$paint(3.5, 4.0, 26, 12);

// Presented-to line
$paint(26, 37.5, 48, 5.5);

// Name + line
$paint(18, 41.5, 64, 13);

// Body/lorem
$paint(22, 53.5, 56, 18);

// Date / signature value zones (keep DATE / SIGNATURE labels)
$paint(12, 73, 22, 6);
$paint(66, 73, 22, 6);

imagejpeg($im, $dstJpeg, 95);
imagepng($im, $dstBlank, 6);
imagedestroy($im);

echo "Wrote blank PNG + JPEG\n";
