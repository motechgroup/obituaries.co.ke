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
        Schema::table('obituary_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('obituary_reports', 'is_system_flagged')) {
                $table->boolean('is_system_flagged')->default(false)->after('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obituary_reports', function (Blueprint $table) {
            if (Schema::hasColumn('obituary_reports', 'is_system_flagged')) {
                $table->dropColumn('is_system_flagged');
            }
        });
    }
};
