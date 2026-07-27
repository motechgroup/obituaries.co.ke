<?php
/**
 * Standalone Web Git Pull Helper for Obituaries.co.ke
 * Access via browser: https://obituaries.co.ke/git-pull.php
 */

use Illuminate\Support\Facades\Artisan;

$baseDir = dirname(__DIR__);

require_once $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Git Pull Codebase | Obituaries.co.ke</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0b101d; color: #e2e8f0; padding: 40px 20px; margin: 0; }
        .container { max-width: 700px; margin: 0 auto; background: #1e293b; border: 1px solid #334155; border-radius: 20px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h1 { margin-top: 0; color: #f8fafc; font-size: 24px; font-weight: 800; display: flex; items-center: center; gap: 10px; }
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
<div class='container'>
    <div style='display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;'>
        <h1>🔄 Git Pull Codebase</h1>
        <span class='badge'>Obituaries.co.ke</span>
    </div>";

try {
    $basePath = base_path();
    $command = "cd " . escapeshellarg($basePath) . " && git config --global --add safe.directory " . escapeshellarg($basePath) . " 2>&1 && git pull origin main 2>&1";
    $output = shell_exec($command);

    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    echo "<div class='alert-success'>
            ✅ Git Pull Completed & Cache Cleared!
          </div>";

    echo "<h3 style='color: #cbd5e1; margin-bottom: 8px;'>Git Command Output:</h3>";
    echo "<pre>" . htmlspecialchars(trim($output ?: "Already up to date.")) . "</pre>";

    echo "<div class='btn-group'>
            <a href='/' class='btn btn-primary'>Go to Website &rarr;</a>
            <a href='/admin/login' class='btn btn-secondary'>Admin Portal &rarr;</a>
            <a href='/run-migrations.php' class='btn btn-secondary'>Run Migrations &rarr;</a>
          </div>";

} catch (\Throwable $e) {
    echo "<div class='alert-error'>
            ❌ Git Pull Failed! Exception: " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "</div>
</body>
</html>";
