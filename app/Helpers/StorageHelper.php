<?php

namespace App\Helpers;

class StorageHelper
{
    /**
     * Permanently stores a file in storage/app/public AND copies it to public/storage/
     * so that Apache serves static files directly on shared hosting without requiring symlinks.
     */
    public static function savePublicFile($file, string $folder): string
    {
        // 1. Save using standard Laravel storage disk
        $path = $file->store($folder, 'public');
        $sourcePath = storage_path('app/public/' . $path);

        // 2. Compress & scale image using PHP GD to save payload size
        self::compressAndScaleImage($sourcePath, 800, 82);

        // 3. Immediately mirror to public/storage/ for instant web access
        try {
            $destPath = public_path('storage/' . $path);
            $destDir = dirname($destPath);

            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            @copy($sourcePath, $destPath);
        } catch (\Throwable $e) {
            // Silently handle exceptions
        }

        return $path;
    }

    /**
     * Ensures an asset path exists in public/storage/, copying on demand if missing.
     */
    public static function ensurePublicCopy(string $relativePath): void
    {
        try {
            $source = storage_path('app/public/' . ltrim($relativePath, '/'));
            $destination = public_path('storage/' . ltrim($relativePath, '/'));

            if (file_exists($source)) {
                self::compressAndScaleImage($source, 800, 82);

                if (!file_exists($destination)) {
                    @mkdir(dirname($destination), 0755, true);
                    @copy($source, $destination);
                }
            }
        } catch (\Throwable $e) {
            // Silently handle exceptions
        }
    }

    /**
     * Resizes and compresses an image file using PHP GD extension.
     * Max dimension defaults to 600px; quality defaults to 75%.
     */
    public static function compressAndScaleImage(string $fullPath, int $maxDimension = 600, int $quality = 75): void
    {
        if (!file_exists($fullPath) || !extension_loaded('gd')) {
            return;
        }

        $imageInfo = @getimagesize($fullPath);
        if (!$imageInfo) {
            return;
        }

        $mime = $imageInfo['mime'] ?? '';
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($width <= 0 || $height <= 0) {
            return;
        }

        // Skip if image is already small enough (under 75KB) and within max dimensions
        if ($width <= $maxDimension && $height <= $maxDimension && filesize($fullPath) < 75000) {
            return;
        }

        // Calculate target dimensions preserving aspect ratio
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width >= $height) {
                $newWidth = $maxDimension;
                $newHeight = (int)round(($height / $width) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int)round(($width / $height) * $maxDimension);
            }
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create GD resource from original
        $srcImage = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($fullPath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = @imagecreatefromwebp($fullPath);
                }
                break;
        }

        if (!$srcImage) {
            return;
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha transparency for PNG / WebP
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save compressed image back to disk
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                @imagejpeg($dstImage, $fullPath, $quality);
                break;
            case 'image/png':
                $pngQuality = (int)round((100 - $quality) / 10);
                @imagepng($dstImage, $fullPath, min(9, max(0, $pngQuality)));
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    @imagewebp($dstImage, $fullPath, $quality);
                }
                break;
        }

        @imagedestroy($srcImage);
        @imagedestroy($dstImage);
    }

    /**
     * Sanitizes HTML content allowing safe formatting tags for Admin/Editor obituary tributes.
     */
    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Allowed formatting tags: bold, italics, underline, headings, lists, quotes, breaks, paragraphs
        $allowedTags = '<b><i><u><strong><em><p><br><ul><ol><li><h3><h4><h5><blockquote><span><div>';
        
        $cleaned = strip_tags($html, $allowedTags);
        
        // Remove dangerous inline JS attributes
        $cleaned = preg_replace('/on[a-z]+\s*=\s*(["\']).*?\1/i', '', $cleaned);
        $cleaned = preg_replace('/javascript\s*:/i', '', $cleaned);

        return $cleaned;
    }

    /**
     * Formats biography HTML ensuring proper paragraph spacing (<p> tags), line breaks, and sanitization.
     */
    public static function formatBiographyHtml(?string $biography): string
    {
        if (empty($biography)) {
            return '';
        }

        $cleaned = self::sanitizeHtml($biography);

        // Check if string contains paragraph or block level HTML tags
        $hasBlockTags = (bool)preg_match('/<(p|h[1-6]|ul|ol|blockquote|div)\b/i', $cleaned);

        if (!$hasBlockTags) {
            // Split by single or double newlines and wrap paragraphs in <p> tags
            $lines = preg_split('/\r\n|\r|\n/', $cleaned);
            $paragraphs = [];
            $currentParagraph = [];

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) {
                    if (!empty($currentParagraph)) {
                        $paragraphs[] = '<p>' . implode('<br>', $currentParagraph) . '</p>';
                        $currentParagraph = [];
                    }
                } else {
                    $currentParagraph[] = $trimmed;
                }
            }

            if (!empty($currentParagraph)) {
                $paragraphs[] = '<p>' . implode('<br>', $currentParagraph) . '</p>';
            }

            return implode("\n", $paragraphs);
        }

        return $cleaned;
    }
}
