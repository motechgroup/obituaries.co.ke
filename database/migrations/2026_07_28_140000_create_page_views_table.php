<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('route_name')->nullable();
            $table->foreignId('obituary_id')->nullable()->constrained('obituaries')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device_type', 20)->default('desktop');
            $table->string('referer', 500)->nullable();
            $table->string('referer_host', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['created_at', 'device_type']);
            $table->index('obituary_id');
            $table->index('referer_host');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
