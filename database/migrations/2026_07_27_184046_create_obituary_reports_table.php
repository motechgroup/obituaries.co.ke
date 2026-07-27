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
        Schema::create('obituary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obituary_id')->constrained()->onDelete('cascade');
            $table->string('reporter_name');
            $table->string('reporter_email');
            $table->string('reporter_phone')->nullable();
            $table->string('reason');
            $table->text('details');
            $table->string('status')->default('pending'); // pending, reviewed, resolved, dismissed
            $table->foreignId('resolved_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituary_reports');
    }
};
