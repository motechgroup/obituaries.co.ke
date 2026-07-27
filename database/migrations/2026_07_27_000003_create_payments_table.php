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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obituary_id')->constrained('obituaries')->cascadeOnDelete();
            $table->string('phone_number');
            $table->decimal('amount', 10, 2)->default(500.00);
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('mpesa_receipt_number')->nullable()->index();
            
            // status: pending, completed, failed, cancelled
            $table->string('status')->default('pending');
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->json('raw_callback_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
