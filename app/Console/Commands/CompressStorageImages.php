<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use Illuminate\Console\Command;

class CompressStorageImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obituaries:compress-images {--max=800} {--quality=82}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch compress and resize all existing uploaded obituary photos in storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $max = (int) $this->option('max');
        $quality = (int) $this->option('quality');

        $directories = [
            storage_path('app/public/obituaries'),
            storage_path('app/public/gallery'),
            public_path('storage/obituaries'),
            public_path('storage/gallery'),
        ];

        $totalCompressed = 0;

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                continue;
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $beforeSize = $file->getSize();
                        StorageHelper::compressAndScaleImage($file->getRealPath(), $max, $quality);
                        clearstatcache(true, $file->getRealPath());
                        $afterSize = filesize($file->getRealPath());

                        if ($afterSize < $beforeSize) {
                            $totalCompressed++;
                        }
                    }
                }
            }
        }

        $this->info("Successfully processed and optimized {$totalCompressed} images.");
        return Command::SUCCESS;
    }
}
