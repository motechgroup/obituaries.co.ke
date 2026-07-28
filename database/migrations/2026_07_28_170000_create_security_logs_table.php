<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('device_type')->default('Desktop');
            $table->string('action'); // e.g. obituary_submitted, payment_initiated, admin_login, report_submitted, ip_blocked
            $table->string('severity')->default('info'); // info, warning, danger, critical
            $table->foreignId('obituary_id')->nullable()->constrained('obituaries')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
