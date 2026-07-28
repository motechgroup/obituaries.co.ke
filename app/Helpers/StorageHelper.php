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

        // 2. Immediately mirror to public/storage/ for instant web access
        try {
            $destPath = public_path('storage/' . $path);
            $destDir = dirname($destPath);

            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            @copy(storage_path('app/public/' . $path), $destPath);
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

            if (file_exists($source) && !file_exists($destination)) {
                @mkdir(dirname($destination), 0755, true);
                @copy($source, $destination);
            }
        } catch (\Throwable $e) {
            // Silently handle exceptions
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
}
