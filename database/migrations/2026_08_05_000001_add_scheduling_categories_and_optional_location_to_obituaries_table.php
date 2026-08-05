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
        Schema::table('obituaries', function (Blueprint $table) {
            if (!Schema::hasColumn('obituaries', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('obituaries', 'category')) {
                $table->string('category')->default('Death Announcement')->after('full_name');
            }
            $table->string('county')->nullable()->change();
            $table->string('town')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obituaries', function (Blueprint $table) {
            if (Schema::hasColumn('obituaries', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('obituaries', 'category')) {
                $table->dropColumn('category');
            }
            $table->string('county')->nullable(false)->change();
            $table->string('town')->nullable(false)->change();
        });
    }
};
