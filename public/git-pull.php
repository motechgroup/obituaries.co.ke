<?php

/**
 * Pure PHP Web Codebase Deployer for Shared Hosting
 * Works on Truehost / cPanel shared hosting without shell_exec or terminal access.
 * Access via browser: https://obituaries.co.ke/git-pull.php
 */

define('LARAVEL_START', microtime(true));

$baseDir = dirname(__DIR__);

// Load Autoloader & Bootstrap Laravel
$autoloadPath = $baseDir . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: Vendor autoloader not found at {$autoloadPath}");
}
require_once $autoloadPath;

$appPath = $baseDir . '/bootstrap/app.php';
if (!file_exists($appPath)) {
    die("Error: Bootstrap app not found at {$appPath}");
}
$app = require_once $appPath;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

$zipUrl = "https://github.com/motechgroup/obituaries.co.ke/archive/refs/heads/main.zip";
$tempZipPath = storage_path('app/latest-main.zip');
$extractTempPath = storage_path('app/git-extracted');

$updatedFilesCount = 0;
$errorMsg = null;

try {
    // 1. Download repository ZIP from GitHub using PHP cURL (with follow redirects)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $zipUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ObituariesWebDeployer/1.0');
    $zipData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 || empty($zipData)) {
        throw new Exception("Failed to download repository ZIP from GitHub (HTTP Code: {$httpCode}). " . ($curlError ?: 'Data empty.'));
    }

    // 2. Save ZIP payload to storage
    file_put_contents($tempZipPath, $zipData);

    // 3. Extract ZIP using PHP ZipArchive
    if (!class_exists('ZipArchive')) {
        throw new Exception("PHP ZipArchive extension is not enabled on this server.");
    }

    $zip = new ZipArchive();
    if ($zip->open($tempZipPath) !== true) {
        throw new Exception("Unable to open downloaded ZIP package.");
    }

    // Ensure extraction folder exists
    if (!file_exists($extractTempPath)) {
        @mkdir($extractTempPath, 0755, true);
    }

    $zip->extractTo($extractTempPath);
    $zip->close();

    // 4. Locate extracted directory (e.g. obituaries.co.ke-main)
    $extractedDirs = glob($extractTempPath . '/*', GLOB_ONLYDIR);
    if (empty($extractedDirs)) {
        throw new Exception("Extracted ZIP folder structure not found.");
    }
    $sourceRoot = $extractedDirs[0];

    // 5. Copy updated files recursively to base_path(), ignoring sensitive files
    $skipPaths = ['.env', '.git', 'storage/app/public', 'storage/framework/cache', 'storage/framework/views', 'public/storage'];

    $copyRecursive = function ($src, $dst) use (&$copyRecursive, $sourceRoot, &$updatedFilesCount, $skipPaths) {
        $dir = @opendir($src);
        if (!$dir) return;

        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..') continue;

            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;

            $relative = ltrim(str_replace($sourceRoot, '', $srcPath), '/');

            // Check if path should be skipped
            $shouldSkip = false;
            foreach ($skipPaths as $skip) {
                if (str_starts_with($relative, $skip)) {
                    $shouldSkip = true;
                    break;
                }
            }

            if ($shouldSkip) continue;

            if (is_dir($srcPath)) {
                $copyRecursive($srcPath, $dstPath);
            } else {
                @mkdir(dirname($dstPath), 0755, true);
                if (@copy($srcPath, $dstPath)) {
                    $updatedFilesCount++;
                }
            }
        }
        closedir($dir);
    };

    $copyRecursive($sourceRoot, base_path());

    // Clean up temporary ZIP
    @unlink($tempZipPath);

    // 6. Robustly Purge Stale File Cache & Compiled Blade Views
    $purgeDir = function ($dirPath) use (&$purgeDir) {
        if (!file_exists($dirPath)) return;
        $items = @scandir($dirPath);
        if (!$items) return;
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dirPath . '/' . $item;
            if (is_dir($path)) {
                $purgeDir($path);
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }
    };

    $purgeDir(storage_path('framework/cache/data'));
    $purgeDir(storage_path('framework/views'));

    try {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => 'AdvertisingSeeder', '--force' => true]);
    } catch (\Throwable $e) {}

        // Auto-compress existing live storage photos using ImageOptimizerEngine
        try {
            $optimizer = new \App\Services\ImageOptimizerEngine(800, 80);
            $dirs = [
                __DIR__ . '/storage',
                dirname(__DIR__) . '/storage/app/public',
            ];
            foreach ($dirs as $dir) {
                if (file_exists($dir)) {
                    $optimizer->optimizeDirectory($dir);
                }
            }
        } catch (\Throwable $e) {}
    } catch (\Throwable $e) {}

} catch (\Throwable $e) {
    $errorMsg = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Deploy Codebase | Obituaries.co.ke</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0b101d; color: #e2e8f0; padding: 40px 20px; margin: 0; }
        .container { max-width: 700px; margin: 0 auto; background: #1e293b; border: 1px solid #334155; border-radius: 20px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h1 { margin-top: 0; color: #f8fafc; font-size: 24px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .badge { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 1px; }
        .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 600; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; }
        pre { background: #0f172a; color: #38bdf8; padding: 16px; border-radius: 12px; font-size: 13px; font-family: monospace; overflow-x: auto; border: 1px solid #1e293b; }
        .btn-group { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .btn-primary { background: #38bdf8; color: #0f172a; }
        .btn-primary:hover { background: #0284c7; color: #fff; }
        .btn-secondary { background: #334155; color: #f8fafc; }
        .btn-secondary:hover { background: #475569; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-namespace:space-between; align-items:center; margin-bottom: 20px;">
        <h1>✨ Codebase Update (Pure PHP)</h1>
        <span class="badge">Obituaries.co.ke</span>
    </div>

    <?php if ($errorMsg): ?>
        <div class="alert-error">
            ❌ Deployment Error: <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php else: ?>
        <div class="alert-success">
            ✅ Codebase Successfully Synchronized & Cache Cleared!
        </div>
        <p style="font-size: 14px; color: #cbd5e1;">
            Downloaded and updated <strong><?= $updatedFilesCount ?></strong> files directly from GitHub <code>main</code> branch without shell execution requirements.
        </p>
    <?php endif; ?>

    <div class="btn-group">
        <a href="/" class="btn btn-primary">Go to Website &rarr;</a>
        <a href="/access" class="btn btn-secondary">Admin Portal &rarr;</a>
        <a href="/run-migrations.php" class="btn btn-secondary">Run Migrations &rarr;</a>
    </div>
</div>
</body>
</html>
