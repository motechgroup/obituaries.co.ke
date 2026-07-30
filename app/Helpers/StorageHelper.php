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
     * Resizes and compresses an image file using ImageOptimizerEngine.
     * Max dimension defaults to 800px; quality defaults to 80%.
     */
    public static function compressAndScaleImage(string $fullPath, int $maxDimension = 800, int $quality = 80): void
    {
        try {
            $optimizer = new \App\Services\ImageOptimizerEngine($maxDimension, $quality);
            $optimizer->optimizeImage($fullPath);
        } catch (\Throwable $e) {
            // Silently fallback if anything fails
        }
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
