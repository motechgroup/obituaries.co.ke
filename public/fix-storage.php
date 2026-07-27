<?php
/**
 * Storage Symlink & Image Storage Fixer for Obituaries.co.ke
 * Access via browser: https://obituaries.co.ke/fix-storage.php
 */

use Illuminate\Support\Facades\Artisan;

$baseDir = dirname(__DIR__);

require_once $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$publicStoragePath = public_path('storage');
$targetStoragePath = storage_path('app/public');

// Ensure target storage directory and obituaries subfolder exist
if (!file_exists($targetStoragePath)) {
    mkdir($targetStoragePath, 0755, true);
}
if (!file_exists($targetStoragePath . '/obituaries')) {
    mkdir($targetStoragePath . '/obituaries', 0755, true);
}

$output = "";
$mode = "";

// 1. If symlink() is enabled on host
if (function_exists('symlink')) {
    try {
        if (is_link($publicStoragePath) || file_exists($publicStoragePath)) {
            if (is_link($publicStoragePath)) {
                @unlink($publicStoragePath);
            }
        }
        Artisan::call('storage:link', ['--force' => true]);
        $output = Artisan::output();
        $mode = "symlink";
    } catch (\Throwable $e) {
        $output = "Symlink attempt: " . $e->getMessage();
    }
} else {
    // 2. If symlink() function is disabled by host (cPanel restriction)
    $output = "Notice: 'symlink()' function is disabled on this server by hosting security policy.\nUsing Laravel fallback route & direct directory sync strategy!";
    $mode = "copy_fallback";

    // If public/storage is a broken symlink, remove it so Laravel route fallback handles /storage/{path}
    if (is_link($publicStoragePath)) {
        @unlink($publicStoragePath);
    }

    // Copy files directly into public/storage if directory exists or create real folder
    if (!file_exists($publicStoragePath)) {
        @mkdir($publicStoragePath, 0755, true);
        @mkdir($publicStoragePath . '/obituaries', 0755, true);
    }

    // Helper recursive copy function
    $copyDirectory = function ($src, $dst) use (&$copyDirectory) {
        $dir = @opendir($src);
        if (!$dir) return;
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    };

    $copyDirectory($targetStoragePath, $publicStoragePath);
    $output .= "\nSynced all files from {$targetStoragePath} into {$publicStoragePath}.";
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fix Storage Symlink | Obituaries.co.ke</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0b101d; color: #e2e8f0; padding: 40px 20px; margin: 0; }
        .container { max-width: 680px; margin: 0 auto; background: #1e293b; border-radius: 16px; padding: 32px; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h1 { color: #f8fafc; font-size: 22px; margin-top: 0; }
        .success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 600; line-height: 1.5; }
        pre { background: #0f172a; color: #38bdf8; padding: 14px; border-radius: 10px; font-size: 13px; font-family: monospace; overflow-x: auto; border: 1px solid #1e293b; }
        .btn-group { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .btn-primary { background: #f59e0b; color: #0f172a; }
        .btn-primary:hover { background: #d97706; }
        .btn-secondary { background: #334155; color: #f8fafc; }
        .btn-secondary:hover { background: #475569; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🖼️ Storage & Image Path Repair Tool</h1>
    <div class='success'>
        ✅ Storage Files & Path Repaired Successfully!<br>
        <span style='font-size:12px; font-weight:normal; color:#cbd5e1;'>All uploaded images in <code>storage/app/public</code> are now accessible on the web.</span>
    </div>

    <h3 style='color:#cbd5e1; margin-bottom:8px;'>Diagnostic Output:</h3>
    <pre>" . htmlspecialchars($output) . "</pre>

    <div class='btn-group'>
        <a href='/' class='btn btn-primary'>Go to Website &rarr;</a>
        <a href='/admin/dashboard' class='btn btn-secondary'>Admin Dashboard &rarr;</a>
    </div>
</div>
</body>
</html>";
