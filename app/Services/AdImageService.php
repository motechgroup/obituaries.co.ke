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

        // 1. Validate MIME type (PNG and JPEG/JPG only)
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo || !in_array(strtolower($imageInfo['mime'] ?? ''), $allowedMimes, true)) {
            $errors[] = "Only PNG and JPEG/JPG image formats are allowed.";
            return ['valid' => false, 'errors' => $errors];
        }

        // 2. Max size 5MB (5120 KB)
        $maxKb = 5120;
        $fileSizeKb = round($file->getSize() / 1024);
        if ($fileSizeKb > $maxKb) {
            $errors[] = "File size exceeds the maximum limit of 5MB (Uploaded: " . round($fileSizeKb / 1024, 2) . "MB).";
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Enforce dimension bounds
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

        // Mirror webp & thumb to public/storage/ for instant web access without symlinks
        StorageHelper::ensurePublicCopy($webpPath);
        StorageHelper::ensurePublicCopy($thumbPath);

        return [
            'banner_path' => $originalPath,
            'banner_webp_path' => file_exists($webpFullPath) ? $webpPath : $originalPath,
            'thumbnail_path' => file_exists($thumbFullPath) ? $thumbPath : $originalPath,
        ];
    }
}
