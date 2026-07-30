<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceOfferDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_pricing_details_calculates_correct_discount()
    {
        Setting::set('obituary_publishing_cost', '2000');
        Setting::set('obituary_discount_percentage', '20');
        Setting::set('obituary_offer_enabled', '1');

        $pricing = Setting::getPricingDetails();

        $this->assertEquals(2000.0, $pricing['base_price']);
        $this->assertEquals(20.0, $pricing['discount_percent']);
        $this->assertEquals(400.0, $pricing['savings']);
        $this->assertEquals(1600.0, $pricing['final_price']);
        $this->assertTrue($pricing['has_offer']);
    }

    public function test_setting_pricing_details_returns_full_price_when_offer_disabled()
    {
        Setting::set('obituary_publishing_cost', '2000');
        Setting::set('obituary_discount_percentage', '20');
        Setting::set('obituary_offer_enabled', '0');

        $pricing = Setting::getPricingDetails();

        $this->assertEquals(2000.0, $pricing['base_price']);
        $this->assertEquals(0.0, $pricing['discount_percent']);
        $this->assertEquals(0.0, $pricing['savings']);
        $this->assertEquals(2000.0, $pricing['final_price']);
        $this->assertFalse($pricing['has_offer']);
    }

    public function test_admin_can_update_pricing_and_discount_percentage()
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@obituaries.co.ke',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.update'), [
                'obituary_publishing_cost' => 3000,
                'obituary_discount_percentage' => 25,
                'obituary_offer_enabled' => '1',
            ]);

        $response->assertRedirect();

        $this->assertEquals('3000', Setting::get('obituary_publishing_cost'));
        $this->assertEquals('25', Setting::get('obituary_discount_percentage'));
        $this->assertEquals('1', Setting::get('obituary_offer_enabled'));

        $pricing = Setting::getPricingDetails();
        $this->assertEquals(3000.0, $pricing['base_price']);
        $this->assertEquals(25.0, $pricing['discount_percent']);
        $this->assertEquals(750.0, $pricing['savings']);
        $this->assertEquals(2250.0, $pricing['final_price']);
    }

    public function test_visitor_submit_page_displays_offer_and_strikethrough_price()
    {
        Setting::set('obituary_publishing_cost', '2000');
        Setting::set('obituary_discount_percentage', '20');
        Setting::set('obituary_offer_enabled', '1');

        $response = $this->get(route('obituaries.submit'));

        $response->assertStatus(200);
        $response->assertSee('20% OFF OFFER');
        $response->assertSee('KES 2,000');
        $response->assertSee('KES 1,600');
    }
}
