<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizerEngine;
use Illuminate\Console\Command;

class CompressStorageImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--max=800} {--quality=80}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch compress and optimize all existing storage photos using ImageOptimizerEngine';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $max = (int) $this->option('max');
        $quality = (int) $this->option('quality');

        $this->info("Initializing Image Compression Engine (Max Dimension: {$max}px, Quality: {$quality}%)...");

        $optimizer = new ImageOptimizerEngine($max, $quality);

        $directories = [
            storage_path('app/public'),
            public_path('storage'),
        ];

        $totalProcessed = 0;
        $totalSaved = 0;

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                continue;
            }

            $stats = $optimizer->optimizeDirectory($dir);
            $totalProcessed += $stats['files_processed'];
            $totalSaved += $stats['total_bytes_saved'];
        }

        $mbSaved = round($totalSaved / (1024 * 1024), 2);
        $this->info("✅ Successfully processed {$totalProcessed} images. Total storage saved: {$mbSaved} MB.");
        return Command::SUCCESS;
    }
}
