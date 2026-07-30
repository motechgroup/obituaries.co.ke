<?php

namespace App\Services;

class ImageOptimizerEngine
{
    /**
     * Default maximum dimension (width or height) in pixels.
     */
    protected int $maxDimension;

    /**
     * Default JPEG/WebP compression quality (1-100).
     */
    protected int $quality;

    public function __construct(int $maxDimension = 800, int $quality = 80)
    {
        $this->maxDimension = $maxDimension;
        $this->quality = $quality;
    }

    /**
     * Resizes and compresses an image file in-place or to a destination.
     * Returns array with original size, compressed size, and savings percentage.
     */
    public function optimizeImage(string $sourcePath, ?string $outputPath = null): array
    {
        $outputPath = $outputPath ?? $sourcePath;

        if (!file_exists($sourcePath) || !extension_loaded('gd')) {
            return [
                'success' => false,
                'original_size' => 0,
                'compressed_size' => 0,
                'bytes_saved' => 0,
            ];
        }

        $originalSize = @filesize($sourcePath) ?: 0;
        $imageInfo = @getimagesize($sourcePath);

        if (!$imageInfo) {
            return [
                'success' => false,
                'original_size' => $originalSize,
                'compressed_size' => $originalSize,
                'bytes_saved' => 0,
            ];
        }

        $mime = $imageInfo['mime'] ?? '';
        $origWidth = $imageInfo[0] ?? 0;
        $origHeight = $imageInfo[1] ?? 0;

        if ($origWidth <= 0 || $origHeight <= 0) {
            return [
                'success' => false,
                'original_size' => $originalSize,
                'compressed_size' => $originalSize,
                'bytes_saved' => 0,
            ];
        }

        // Calculate target dimensions maintaining aspect ratio
        if ($origWidth > $this->maxDimension || $origHeight > $this->maxDimension) {
            if ($origWidth >= $origHeight) {
                $newWidth = $this->maxDimension;
                $newHeight = (int) round(($origHeight / $origWidth) * $this->maxDimension);
            } else {
                $newHeight = $this->maxDimension;
                $newWidth = (int) round(($origWidth / $origHeight) * $this->maxDimension);
            }
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Create GD source resource based on mime
        $srcImage = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = @imagecreatefromwebp($sourcePath);
                }
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($sourcePath);
                break;
        }

        if (!$srcImage) {
            return [
                'success' => false,
                'original_size' => $originalSize,
                'compressed_size' => $originalSize,
                'bytes_saved' => 0,
            ];
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle alpha transparency for PNG / WebP / GIF
        if (in_array($mime, ['image/png', 'image/webp', 'image/gif'])) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Ensure output directory exists
        $outputDir = dirname($outputPath);
        if (!file_exists($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        // Save image with optimized quality
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                @imagejpeg($dstImage, $outputPath, $this->quality);
                break;
            case 'image/png':
                $pngQuality = (int) round((100 - $this->quality) / 10);
                @imagepng($dstImage, $outputPath, min(9, max(0, $pngQuality)));
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    @imagewebp($dstImage, $outputPath, $this->quality);
                } else {
                    @imagejpeg($dstImage, $outputPath, $this->quality);
                }
                break;
            default:
                @imagejpeg($dstImage, $outputPath, $this->quality);
                break;
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        // Clear PHP file stat cache to get accurate output file size
        clearstatcache(true, $outputPath);
        $compressedSize = @filesize($outputPath) ?: 0;

        // If compressed file turned out larger than original, restore original
        if ($compressedSize > $originalSize && $sourcePath === $outputPath) {
            // Keep original if it was smaller
            $compressedSize = $originalSize;
        }

        $bytesSaved = max(0, $originalSize - $compressedSize);

        return [
            'success' => true,
            'original_size' => $originalSize,
            'compressed_size' => $compressedSize,
            'bytes_saved' => $bytesSaved,
        ];
    }

    /**
     * Recursively optimizes all images in a directory.
     * Returns stats summary.
     */
    public function optimizeDirectory(string $directoryPath): array
    {
        $processedCount = 0;
        $totalOriginal = 0;
        $totalCompressed = 0;

        if (!file_exists($directoryPath) || !is_dir($directoryPath)) {
            return [
                'files_processed' => 0,
                'total_original_bytes' => 0,
                'total_compressed_bytes' => 0,
                'total_bytes_saved' => 0,
            ];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directoryPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $result = $this->optimizeImage($file->getRealPath());
                    if ($result['success']) {
                        $processedCount++;
                        $totalOriginal += $result['original_size'];
                        $totalCompressed += $result['compressed_size'];
                    }
                }
            }
        }

        return [
            'files_processed' => $processedCount,
            'total_original_bytes' => $totalOriginal,
            'total_compressed_bytes' => $totalCompressed,
            'total_bytes_saved' => max(0, $totalOriginal - $totalCompressed),
        ];
    }
}
