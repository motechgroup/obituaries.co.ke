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
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
            if (!Schema::hasColumn('admins', 'phone')) {
                $table->string('phone')->nullable()->after('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('admins', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
