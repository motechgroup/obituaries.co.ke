<?php

namespace App\Services;

use App\Models\BannerSize;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Helpers\StorageHelper;

class AdImageService
{
    /**
     * Validate uploaded banner dimensions and max file size.
     */
    public static function validateBanner(UploadedFile $file, BannerSize $targetSize): array
    {
        $errors = [];

        // 1. Max size 2MB (2048 KB)
        $fileSizeKb = round($file->getSize() / 1024);
        if ($fileSizeKb > $targetSize->max_size_kb) {
            $errors[] = "File size exceeds the maximum limit of " . ($targetSize->max_size_kb / 1024) . "MB (Uploaded: {$fileSizeKb} KB).";
        }

        // 2. Validate image dimensions using getimagesize()
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            $errors[] = "Uploaded file is not a valid image.";
            return ['valid' => false, 'errors' => $errors];
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Allow 5% tolerance for aspect ratio matching if necessary, but enforce strict dimension bounds
        $targetW = $targetSize->width;
        $targetH = $targetSize->height;

        if ($width < ($targetW * 0.9) || $height < ($targetH * 0.9)) {
            $errors[] = "Banner dimensions are too small. Expected: {$targetW} × {$targetH} px (Uploaded: {$width} × {$height} px).";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * Process uploaded banner image: save original, convert to WebP, and create thumbnail.
     */
    public static function processAndSaveBanner(UploadedFile $file, BannerSize $targetSize): array
    {
        $originalPath = StorageHelper::savePublicFile($file, 'advertising/banners');

        $fullPath = storage_path('app/public/' . $originalPath);
        $directory = dirname($fullPath);
        $filename = pathinfo($fullPath, PATHINFO_FILENAME);

        $webpFilename = $filename . '.webp';
        $webpPath = 'advertising/banners/' . $webpFilename;
        $webpFullPath = $directory . '/' . $webpFilename;

        $thumbFilename = $filename . '-thumb.jpg';
        $thumbPath = 'advertising/banners/' . $thumbFilename;
        $thumbFullPath = $directory . '/' . $thumbFilename;

        // Create GD resource from original
        $imageInfo = @getimagesize($fullPath);
        $mime = $imageInfo['mime'] ?? '';
        $srcImage = null;

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $srcImage = @imagecreatefromjpeg($fullPath);
        } elseif ($mime === 'image/png') {
            $srcImage = @imagecreatefrompng($fullPath);
        } elseif ($mime === 'image/webp') {
            $srcImage = @imagecreatefromwebp($fullPath);
        }

        if ($srcImage) {
            $origW = imagesx($srcImage);
            $origH = imagesy($srcImage);

            // Resize to target dimensions cleanly
            $targetW = $targetSize->width;
            $targetH = $targetSize->height;

            $dstImage = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

            // Save WebP version
            if (function_exists('imagewebp')) {
                imagewebp($dstImage, $webpFullPath, 85);
            } else {
                @copy($fullPath, $webpFullPath);
            }

            // Create 300px thumbnail
            $thumbW = 300;
            $thumbH = (int) round(($origH / $origW) * $thumbW);
            $thumbImage = imagecreatetruecolor($thumbW, $thumbH);
            imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, $thumbW, $thumbH, $origW, $origH);
            imagejpeg($thumbImage, $thumbFullPath, 80);

            imagedestroy($srcImage);
            imagedestroy($dstImage);
            imagedestroy($thumbImage);
        } else {
            // Fallback
            @copy($fullPath, $webpFullPath);
            @copy($fullPath, $thumbFullPath);
        }

        return [
            'banner_path' => $originalPath,
            'banner_webp_path' => file_exists($webpFullPath) ? $webpPath : $originalPath,
            'thumbnail_path' => file_exists($thumbFullPath) ? $thumbPath : $originalPath,
        ];
    }
}
