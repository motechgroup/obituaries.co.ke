<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\BusinessCategory;
use Database\Seeders\AdvertisingSeeder;

return new class extends Migration
{
    public function up(): void
    {
        try {
            (new AdvertisingSeeder())->run();
        } catch (\Throwable $e) {
            // Silently log or handle exceptions
        }
    }

    public function down(): void
    {
        // No rollback action needed
    }
};
