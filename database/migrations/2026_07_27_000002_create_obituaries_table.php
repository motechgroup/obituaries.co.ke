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
        Schema::create('obituaries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('full_name');
            $table->string('photo')->nullable();
            $table->date('date_of_birth');
            $table->date('date_of_death');
            $table->string('county');
            $table->string('town');
            $table->text('biography');
            $table->date('funeral_date')->nullable();
            $table->string('burial_location')->nullable();
            $table->string('church_service_location')->nullable();
            $table->string('programme_file')->nullable();
            
            // Submitter Details
            $table->string('submitter_name');
            $table->string('submitter_phone');
            $table->string('submitter_email')->nullable();
            $table->string('relationship'); // Child, Spouse, Parent, Relative, Friend, Organization
            $table->boolean('family_permission_confirmed')->default(true);

            // Status tracking
            // status: draft, pending_payment, payment_confirmed, pending_verification, published, rejected
            $table->string('status')->default('pending_payment');
            
            // Verification tracking
            // verification_status: unverified, pending, verified, rejected
            $table->string('verification_status')->default('unverified');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituaries');
    }
};
