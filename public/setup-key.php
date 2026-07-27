<?php
/**
 * Temporary Web Setup Key Generator for Obituaries.co.ke
 */
$baseDir = dirname(__DIR__);
$envFile = $baseDir . '/.env';
$envExample = $baseDir . '/.env.example';

// Create .env from .env.example if missing
if (!file_exists($envFile) && file_exists($envExample)) {
    copy($envExample, $envFile);
}

$key = 'base64:' . base64_encode(random_bytes(32));

if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    if (preg_match('/^APP_KEY=/m', $content)) {
        $content = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $key, $content);
    } else {
        $content .= "\nAPP_KEY=" . $key . "\n";
    }
    file_put_contents($envFile, $content);
    
    echo "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #059669; margin-top:0;'>✅ Success! APP_KEY Generated</h2>";
    echo "<p>Your <code>.env</code> file has been updated with the following key:</p>";
    echo "<pre style='background: #f1f5f9; padding: 15px; border-radius: 8px; font-weight: bold;'>APP_KEY={$key}</pre>";
    echo "<p><a href='/' style='display: inline-block; background: #0f172a; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold;'>Go to Obituaries.co.ke Homepage &rarr;</a></p>";
    echo "</div>";
} else {
    echo "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px;'>";
    echo "<h2>Generated APP_KEY:</h2>";
    echo "<pre style='background: #f1f5f9; padding: 15px; border-radius: 8px;'>{$key}</pre>";
    echo "</div>";
}
