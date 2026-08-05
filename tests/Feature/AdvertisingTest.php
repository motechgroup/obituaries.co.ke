<?php

namespace Tests\Feature;

use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\Advertiser;
use App\Models\BannerSize;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;

use App\Services\AdImageService;
use App\Services\AdPricingEngine;
use App\Services\AdServingService;
use App\Services\AdTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdvertisingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\AdvertisingSeeder::class);
    }

    public function test_advertiser_can_register_and_login()
    {
        $response = $this->post(route('advertiser.register.post'), [
            'business_name' => 'Bungoma Florists & Funeral Care',
            'contact_person' => 'Moses Simiyu',
            'phone_number' => '0711998877',
            'email' => 'simiyu@bungomaflorists.co.ke',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('advertiser.dashboard'));
        $this->assertDatabaseHas('advertisers', ['email' => 'simiyu@bungomaflorists.co.ke']);
        $this->assertTrue(Auth::guard('advertiser')->check());
    }

    public function test_pricing_engine_calculates_correct_prices()
    {
        $placement = AdPlacement::where('slug', 'homepage_after_hero')->first();
        $size = BannerSize::where('slug', 'horizontal-770x195')->first();

        $pricing = AdPricingEngine::calculatePrice(
            $placement->id,
            $size->id,
            now()->format('Y-m-d'),
            now()->addDays(29)->format('Y-m-d'),
            ['Nairobi'],
            false,
            false
        );

        $this->assertEquals(30, $pricing['total_days']);
        $this->assertGreaterThan(0, $pricing['total_price']);
    }

    public function test_ad_serving_engine_serves_matching_county_ad()
    {
        $advertiser = Advertiser::first();
        $profile = BusinessProfile::first();
        $placement = AdPlacement::where('slug', 'homepage_after_hero')->first();
        $size = BannerSize::where('slug', 'horizontal-770x195')->first();

        // Create campaign targeting Kisumu
        $campaign = AdCampaign::create([
            'advertiser_id' => $advertiser->id,
            'business_profile_id' => $profile->id,
            'ad_placement_id' => $placement->id,
            'banner_size_id' => $size->id,
            'name' => 'Kisumu Specific Ad',
            'banner_path' => 'advertising/banners/test.jpg',
            'landing_url' => 'https://example.com',
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(10)->format('Y-m-d'),
            'total_days' => 10,
            'is_national' => false,
            'is_featured' => true,
            'calculated_price' => 5000.00,
            'status' => 'running',
        ]);

        $campaign->counties()->create(['county' => 'Kisumu']);

        $servedAd = AdServingService::getAdForPlacement('homepage_after_hero', 'Kisumu');
        $this->assertNotNull($servedAd);
        $this->assertEquals($campaign->id, $servedAd->id);
    }

    public function test_click_tracking_records_ad_click_and_redirects()
    {
        $campaign = AdCampaign::where('status', 'running')->first();
        $this->assertNotNull($campaign);

        $response = $this->get(route('ad.click', $campaign->id));
        $response->assertRedirect($campaign->landing_url);

        $this->assertDatabaseHas('ad_clicks', [
            'ad_campaign_id' => $campaign->id,
        ]);
    }

    public function test_admin_can_access_campaign_creation_page()
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin User',
            'email' => 'admin_test@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.advertising.campaigns.create'));
        $response->assertStatus(200);
        $response->assertSee('Create & Place Ad Campaign');
    }

    public function test_admin_can_edit_any_campaign()
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin Editor',
            'email' => 'admin_editor@obituaries.co.ke',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
        ]);

        $campaign = AdCampaign::first();
        $this->assertNotNull($campaign);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.advertising.campaigns.edit', $campaign->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Campaign');
    }
}
