# image-resize-application
Image Resize Application

[![License][license-badge]][license-badge-url]


This PHP script provides on-demand image resizing functionality. You can specify width, height, and quality settings using URL parameters.

## Key Features
- **Dynamic Image Resizing**: Automatically resizes images based on specified width ('w') or height ('h')
- **Aspect Ratio Preservation**: Maintains aspect ratio when only one dimension is specified
- **Quality Adjustment**: Adjustable JPEG image quality ('q=0-100')
- **Multiple Format Support**: Handles 'JPEG', 'PNG', and 'GIF' formats

## Installation

### Requirements
- PHP 7.0 or higher
- GD Library extension enabled
- Write permissions for cache directory

### Setup Steps
1. **Download the Script**:
   - Clone this repository or download the `image-resize-application.php` file.

2. **Place the Files**:
   - Upload the PHP script to your web server
   - Create the source image directory (default is `image/`)
   - Create the cache directory (default is `cache/`) and ensure it has write permissions:
     ```
     mkdir cache
     chmod 755 cache
     ```

3. **Configure the Script** (optional):
   - Open `image-resize-application.php` and adjust the settings at the beginning of the file to match your environment.
   - Customize the image directory, cache directory, or allowed file types as needed.

4. **Set Up URL Rewriting** (optional):
   - Create or edit `.htaccess` file in your web root directory
   - Add the rewrite rules shown in the "URL Rewriting" section below
   - Make sure Apache's `mod_rewrite` module is enabled

5. **Testing**:
   - Access your script using the URL patterns shown in the usage section to verify it's working correctly

## Usage
Specify image and size parameters in the URL:
```
example.com/ira.php?path=sample.jpg&w=800&h=600&q=90
```
### Parameters
- **path**: Path to the image file (required)
- **w**: Desired width in pixels
- **h**: Desired height in pixels
- **q**: JPEG quality (0-100, default: 90)

## Configuration Options
The following settings can be configured at the beginning of the script:
```php
<?php
$imagesDir = 'img/';         // Directory containing source images
$cacheDir = 'cache/';         // Directory for storing cached resized images
$defaultQuality = 90;         // Default quality setting
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image formats

if ($width > 4000 || $height > 4000) { // Image size limitation
    header('HTTP/1.1 400 Bad Request');
    exit('Size too large');
}
```
### Important Notes
- The cache directory requires write permissions
- Set appropriate image size limits considering server load
- Consider periodic cleaning if cache grows too large
- This code is designed to run on web servers such as Apache, Nginx, etc.

## an extra
You can use the following .htaccess rules to create clean URLs:

```apache
# Width, height and quality
RewriteRule ^image/(.*)/w(\d+)-h(\d+)-q(\d+)$ image-resize-application.php?path=$1&w=$2&h=$3&q=$4 [L,QSA]
# Width and height
RewriteRule ^image/(.*)/w(\d+)-h(\d+)$ image-resize-application.php?path=$1&w=$2&h=$3 [L,QSA]
# Width only
RewriteRule ^image/(.*)/w(\d+)$ image-resize-application.php?path=$1&w=$2 [L,QSA]
# Width and quality
RewriteRule ^image/(.*)/w(\d+)-q(\d+)$ image-resize-application.php?path=$1&w=$2&q=$3 [L,QSA]
# Height only
RewriteRule ^image/(.*)/h(\d+)$ image-resize-application.php?path=$1&h=$2 [L,QSA]
# Height and quality
RewriteRule ^image/(.*)/h(\d+)-q(\d+)$ image-resize-application.php?path=$1&h=$2&q=$3 [L,QSA]
# Quality only
RewriteRule ^image/(.*)/q(\d+)$ image-resize-application.php?path=$1&q=$2 [L,QSA]
```

With these rules, you can use URLs like:
```
example.com/image/sample.jpg/w800-h600-q90
example.com/image/sample.jpg/w800
example.com/image/sample.jpg/h600
```

[license-badge]: https://img.shields.io/badge/license-MIT-green.svg
[license-badge-url]: ./LICENSE
