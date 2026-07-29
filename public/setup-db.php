<?php
/**
 * Automated Web Database Migration & Seeding Helper for Obituaries.co.ke
 */
use Illuminate\Support\Facades\Artisan;

$baseDir = dirname(__DIR__);

require_once $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<div style='font-family: sans-serif; max-width: 650px; margin: 40px auto; padding: 30px; border: 1px solid #cbd5e1; border-radius: 16px; background: #fff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);'>";
echo "<h2 style='color: #0f172a; margin-top: 0;'>⚙️ Database Setup & Migration Tool</h2>";

try {
    // 1. Run migrations
    Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = Artisan::output();
    
    // 2. Run seeders
    Artisan::call('db:seed', ['--force' => true]);
    $seedOutput = Artisan::output();

    echo "<div style='background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='margin:0 0 5px 0;'>✅ Database Migrated & Seeded Successfully!</h3>";
    echo "<p style='margin:0; font-size: 13px;'>All tables (sessions, obituaries, payments, admins, etc.) have been created.</p>";
    echo "</div>";

    echo "<h4 style='color: #334155; margin-bottom: 5px;'>Migration Details:</h4>";
    echo "<pre style='background: #0f172a; color: #38bdf8; padding: 12px; border-radius: 8px; font-size: 12px; overflow-x: auto;'>" . htmlspecialchars($migrateOutput) . "</pre>";

    echo "<h4 style='color: #334155; margin-bottom: 5px;'>Seeder Details:</h4>";
    echo "<pre style='background: #0f172a; color: #38bdf8; padding: 12px; border-radius: 8px; font-size: 12px; overflow-x: auto;'>" . htmlspecialchars($seedOutput) . "</pre>";

    echo "<div style='margin-top: 25px;'>";
    echo "<a href='/' style='display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 14px;'>Visit Homepage &rarr;</a> ";
    echo "<a href='/access' style='display: inline-block; background: #d97706; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 14px; margin-left: 10px;'>Admin Portal &rarr;</a>";
    echo "</div>";

} catch (\Throwable $e) {
    echo "<div style='background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='margin:0 0 5px 0;'>❌ Database Error Occurred</h3>";
    echo "<p style='margin:0; font-size: 13px;'>Unable to connect or run migrations. Check your MySQL credentials in <code>.env</code>.</p>";
    echo "</div>";
    echo "<pre style='background: #7f1d1d; color: #fca5a5; padding: 12px; border-radius: 8px; font-size: 12px; overflow-x: auto;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "</div>";
