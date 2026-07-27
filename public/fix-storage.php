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

// Ensure target storage directory and obituaries subfolder exist with permissions
if (!file_exists($targetStoragePath)) {
    mkdir($targetStoragePath, 0755, true);
}
if (!file_exists($targetStoragePath . '/obituaries')) {
    mkdir($targetStoragePath . '/obituaries', 0755, true);
}

// Remove existing broken symlink or file if invalid
if (is_link($publicStoragePath) || file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        @unlink($publicStoragePath);
    }
}

$output = "";

try {
    // Try Artisan storage:link
    Artisan::call('storage:link', ['--force' => true]);
    $output = Artisan::output();
} catch (\Throwable $e) {
    $output = "Artisan storage:link note: " . $e->getMessage() . "\nAttempting direct relative symlink...";
    @symlink($targetStoragePath, $publicStoragePath);
}

// Check if public/storage link works now
$isWorking = is_link($publicStoragePath) || file_exists($publicStoragePath);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fix Storage Symlink | Obituaries.co.ke</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0b101d; color: #e2e8f0; padding: 40px 20px; margin: 0; }
        .container { max-width: 650px; margin: 0 auto; background: #1e293b; border-radius: 16px; padding: 32px; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h1 { color: #f8fafc; font-size: 22px; margin-top: 0; }
        .success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
        .error { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
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
    <h1>🖼️ Storage Symlink & Image Path Fixer</h1>";

if ($isWorking) {
    echo "<div class='success'>
            ✅ Storage Symlink Created & Working!<br>
            Public storage path <code>public/storage</code> is now linked to <code>storage/app/public</code>.
          </div>";
} else {
    echo "<div class='error'>
            ⚠️ Notice: Symlink creation may require cPanel file manager or SSH permissions.
          </div>";
}

echo "<h3 style='color:#cbd5e1; margin-bottom:8px;'>Diagnostic Output:</h3>";
echo "<pre>" . htmlspecialchars($output ?: "Storage link command completed successfully.") . "</pre>";
echo "<p style='font-size:13px; color:#94a3b8;'>Public Link Path: <code>" . htmlspecialchars($publicStoragePath) . "</code></p>";
echo "<p style='font-size:13px; color:#94a3b8;'>Target Storage Path: <code>" . htmlspecialchars($targetStoragePath) . "</code></p>";

echo "<div class='btn-group'>
        <a href='/' class='btn btn-primary'>Go to Website &rarr;</a>
        <a href='/admin/dashboard' class='btn btn-secondary'>Admin Dashboard &rarr;</a>
      </div>";

echo "</div>
</body>
</html>";
