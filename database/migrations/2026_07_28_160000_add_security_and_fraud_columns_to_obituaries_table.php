<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obituaries', function (Blueprint $table) {
            if (!Schema::hasColumn('obituaries', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('obituaries', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('obituaries', 'device_type')) {
                $table->string('device_type')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('obituaries', 'is_flagged_fraud')) {
                $table->boolean('is_flagged_fraud')->default(false)->after('device_type');
            }
            if (!Schema::hasColumn('obituaries', 'fraud_reason')) {
                $table->string('fraud_reason')->nullable()->after('is_flagged_fraud');
            }
        });
    }

    public function down(): void
    {
        Schema::table('obituaries', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent', 'device_type', 'is_flagged_fraud', 'fraud_reason']);
        });
    }
};
