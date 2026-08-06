<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageHelper
{
    /**
     * Get a public URL for a file stored in storage/app/public.
     * Automatically ensures a copy exists in public/storage/ for shared hosting.
     */
    public static function url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // If it's already a full URL or data URI, return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        // Ensure file is mirrored to public/storage/ if direct symlinks are unavailable
        static::ensurePublicCopy($path);

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Get local storage path for a given relative path.
     */
    public static function path(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        return storage_path('app/public/' . ltrim($path, '/'));
    }

    /**
     * Check if a file exists in storage.
     */
    public static function exists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return file_exists(static::path($path)) || Storage::disk('public')->exists($path);
    }

    /**
     * Deletes a file from both storage/app/public and public/storage/.
     */
    public static function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        try {
            // Delete from storage/app/public
            Storage::disk('public')->delete($path);

            // Delete from public/storage
            $publicCopy = public_path('storage/' . ltrim($path, '/'));
            if (file_exists($publicCopy)) {
                @unlink($publicCopy);
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Copies a file from temporary/uploaded location to public storage.
     */
    public static function copyToPublicStorage(string $sourcePath, string $destinationRelativePath): string
    {
        $destinationStorage = storage_path('app/public/' . ltrim($destinationRelativePath, '/'));
        $destinationPublic = public_path('storage/' . ltrim($destinationRelativePath, '/'));

        try {
            @mkdir(dirname($destinationStorage), 0755, true);
            @mkdir(dirname($destinationPublic), 0755, true);

            @copy($sourcePath, $destinationStorage);
            @copy($sourcePath, $destinationPublic);
        } catch (\Throwable $e) {
            // Silently handle exceptions
        }

        return $destinationRelativePath;
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

        // Normalize carriage returns
        $cleaned = str_replace(["\r\n", "\r"], "\n", $cleaned);

        // Convert multiple consecutive <br> tags into paragraph breaks
        $cleaned = preg_replace('/(<br\s*\/?>\s*){2,}/i', "</p><p>", $cleaned);

        // Check if string contains explicit paragraph tags <p>
        $hasParagraphTags = (bool)preg_match('/<p\b/i', $cleaned);

        if (!$hasParagraphTags) {
            // Split text by 2 or more newlines to form separate paragraphs
            $blocks = preg_split('/\n\s*\n/', $cleaned);
            $paragraphs = [];

            foreach ($blocks as $block) {
                $trimmed = trim($block);
                if ($trimmed !== '') {
                    // Convert single newlines within a paragraph block to <br>
                    $paragraphContent = str_replace("\n", '<br>', $trimmed);
                    $paragraphs[] = '<p>' . $paragraphContent . '</p>';
                }
            }

            return implode("\n", $paragraphs);
        } else {
            // Clean up empty paragraphs
            $cleaned = preg_replace('/<p>\s*(<br\s*\/?>)?\s*<\/p>/i', '', $cleaned);
            return $cleaned;
        }
    }
}
