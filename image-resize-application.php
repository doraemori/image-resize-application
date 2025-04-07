<?php
$imagesDir = 'image/'; // 画像が格納されているディレクトリ
$cacheDir = 'cache/';   // リサイズ画像のキャッシュディレクトリ
$defaultQuality = 90;   // デフォルトのJPEG品質（0-100）
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // 許可する画像形式

$imagePath = isset($_GET['path']) ? $_GET['path'] : '';
$width = isset($_GET['w']) ? (int)$_GET['w'] : null;
$height = isset($_GET['h']) ? (int)$_GET['h'] : null;
$quality = isset($_GET['q']) ? (int)$_GET['q'] : $defaultQuality;

$quality = max(0, min(100, $quality));

$imagePath = str_replace(['../', '..\\'], '', $imagePath);
$fullPath = $imagesDir . $imagePath;

if (!file_exists($fullPath)) {
    header('HTTP/1.1 404 Not Found');
    exit('Image not found');
}

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedTypes)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Invalid file type');
}

// サイズの制限（必要に応じて）
if ($width > 4000 || $height > 4000) {
    header('HTTP/1.1 400 Bad Request');
    exit('Size too large');
}

$cacheFileName = md5($imagePath . $width . $height . $quality) . '.' . $ext;
$cachePath = $cacheDir . $cacheFileName;

if (file_exists($cachePath)) {
    deliverImage($cachePath, $ext);
    exit;
}

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

list($origWidth, $origHeight, $type) = getimagesize($fullPath);

$source = createImageFromFile($fullPath, $type);
if (!$source) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Failed to process image');
}

if ($width && !$height) {
    $height = floor($origHeight * ($width / $origWidth));
} elseif (!$width && $height) {
    $width = floor($origWidth * ($height / $origHeight));
} elseif (!$width && !$height) {
    $width = $origWidth;
    $height = $origHeight;
}

$resized = imagecreatetruecolor($width, $height);

if ($type == IMAGETYPE_PNG) {
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
    imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
}

imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

saveImageToFile($resized, $cachePath, $type, $quality);

deliverImage($cachePath, $ext);

imagedestroy($source);
imagedestroy($resized);

function createImageFromFile($file, $type) {
    switch ($type) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($file);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($file);
        case IMAGETYPE_GIF:
            return imagecreatefromgif($file);
        default:
            return false;
    }
}

function saveImageToFile($image, $path, $type, $quality) {
    switch ($type) {
        case IMAGETYPE_JPEG:
            return imagejpeg($image, $path, $quality);
        case IMAGETYPE_PNG:
            // PNGの品質は0-9（圧縮レベル）
            $pngQuality = ($quality - 100) / 11.111111;
            $pngQuality = round(abs($pngQuality));
            return imagepng($image, $path, $pngQuality);
        case IMAGETYPE_GIF:
            return imagegif($image, $path);
    }
}

function deliverImage($path, $ext) {
    $expires = 60 * 60 * 24 * 7;
    header('Cache-Control: public, max-age=' . $expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
    }
    
    header('Content-Length: ' . filesize($path));
    
    readfile($path);
}
?>