<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->foreignId('obituary_id')->nullable()->constrained('obituaries')->nullOnDelete();
            $table->integer('risk_score')->default(50); // 1-100
            $table->string('risk_level')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('threat_type'); // High Frequency, Spam Content, Suspicious Submitter, Multiple Names, Failed Payments
            $table->text('description');
            $table->string('status')->default('open'); // open, dismissed, resolved, blocked
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
