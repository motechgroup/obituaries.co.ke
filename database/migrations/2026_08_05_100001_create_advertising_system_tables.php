<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Advertisers Table
        Schema::create('advertisers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('contact_person');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Business Categories Table
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Business Profiles Table
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained('advertisers')->onDelete('cascade');
            $table->foreignId('business_category_id')->nullable()->constrained('business_categories')->onDelete('set null');
            $table->string('business_name');
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('google_maps_link')->nullable();
            $table->string('county')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'pending', 'suspended'])->default('active');
            $table->timestamps();
        });

        // 4. Banner Sizes Table
        Schema::create('banner_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('width');
            $table->integer('height');
            $table->enum('type', ['horizontal', 'sidebar'])->default('horizontal');
            $table->integer('max_size_kb')->default(2048); // Max 2MB
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 5. Ad Placements Table
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('page_type', ['homepage', 'obituary', 'sidebar', 'search', 'category', 'county'])->default('homepage');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 6. Pivot Table: ad_placement_banner_size
        Schema::create('ad_placement_banner_size', function (Blueprint $table) {
            $table->foreignId('ad_placement_id')->constrained('ad_placements')->onDelete('cascade');
            $table->foreignId('banner_size_id')->constrained('banner_sizes')->onDelete('cascade');
            $table->primary(['ad_placement_id', 'banner_size_id']);
        });

        // 7. Ad Pricings Table
        Schema::create('ad_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_placement_id')->constrained('ad_placements')->onDelete('cascade');
            $table->foreignId('banner_size_id')->constrained('banner_sizes')->onDelete('cascade');
            $table->decimal('daily_rate', 10, 2)->default(500.00);
            $table->decimal('national_daily_rate', 10, 2)->default(1200.00);
            $table->decimal('featured_sur_charge', 10, 2)->default(300.00);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 8. Ad Campaigns Table
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained('advertisers')->onDelete('cascade');
            $table->foreignId('business_profile_id')->nullable()->constrained('business_profiles')->onDelete('cascade');
            $table->foreignId('ad_placement_id')->constrained('ad_placements')->onDelete('cascade');
            $table->foreignId('banner_size_id')->constrained('banner_sizes')->onDelete('cascade');
            $table->foreignId('business_category_id')->nullable()->constrained('business_categories')->onDelete('set null');
            $table->string('name');
            $table->string('banner_path');
            $table->string('banner_webp_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('landing_url');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(1);
            $table->boolean('is_national')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->decimal('calculated_price', 10, 2)->default(0.00);
            $table->enum('status', [
                'draft',
                'submitted',
                'payment_pending',
                'payment_completed',
                'pending_approval',
                'approved',
                'running',
                'expired',
                'rejected'
            ])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // 9. Ad Campaign Counties Table
        Schema::create('ad_campaign_counties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->onDelete('cascade');
            $table->string('county');
            $table->timestamps();
        });

        // 10. Ad Campaign Payments Table
        Schema::create('ad_campaign_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->onDelete('cascade');
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->string('merchant_request_id')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('mpesa_receipt_number')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('result_code')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 11. Ad Impressions Table
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->onDelete('cascade');
            $table->foreignId('ad_placement_id')->nullable()->constrained('ad_placements')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->string('county')->nullable();
            $table->string('country')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->text('referer')->nullable();
            $table->text('page_url')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ad_campaign_id', 'created_at']);
            $table->index(['ip_address', 'ad_campaign_id']);
        });

        // 12. Ad Clicks Table
        Schema::create('ad_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->onDelete('cascade');
            $table->foreignId('ad_placement_id')->nullable()->constrained('ad_placements')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->string('county')->nullable();
            $table->string('country')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->text('referer')->nullable();
            $table->text('page_url')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ad_campaign_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_clicks');
        Schema::dropIfExists('ad_impressions');
        Schema::dropIfExists('ad_campaign_payments');
        Schema::dropIfExists('ad_campaign_counties');
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('ad_pricings');
        Schema::dropIfExists('ad_placement_banner_size');
        Schema::dropIfExists('ad_placements');
        Schema::dropIfExists('banner_sizes');
        Schema::dropIfExists('business_profiles');
        Schema::dropIfExists('business_categories');
        Schema::dropIfExists('advertisers');
    }
};
