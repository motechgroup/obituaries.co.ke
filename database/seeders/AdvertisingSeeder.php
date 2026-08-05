<?php

namespace Database\Seeders;

use App\Models\AdCampaign;
use App\Models\AdCampaignCounty;
use App\Models\AdPlacement;
use App\Models\AdPricing;
use App\Models\Advertiser;
use App\Models\BannerSize;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdvertisingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Business Categories
        $categories = [
            ['name' => 'Funeral Homes', 'icon' => 'home_work', 'description' => 'Professional funeral homes, mortuaries, and compassionate end-of-life care centers.'],
            ['name' => 'Hearse Services', 'icon' => 'airport_shuttle', 'description' => 'Dignified hearse transport, executive motorcade, and body transport services.'],
            ['name' => 'Flowers', 'icon' => 'local_florist', 'description' => 'Wreaths, floral tributes, casket spray arrangements, and sympathy flowers.'],
            ['name' => 'Catering', 'icon' => 'restaurant', 'description' => 'Reputable funeral catering services, food logistics, and refreshment setups.'],
            ['name' => 'Photography', 'icon' => 'photo_camera', 'description' => 'Professional funeral ceremony photography and memory albums.'],
            ['name' => 'Videography', 'icon' => 'videocam', 'description' => 'High quality video coverage and memorial documentary production.'],
            ['name' => 'Live Streaming', 'icon' => 'live_tv', 'description' => 'Virtual live streaming services for diaspora and remote family members.'],
            ['name' => 'Coffins', 'icon' => 'inventory_2', 'description' => 'Premium caskets, traditional coffins, and custom mahogany woodwork.'],
            ['name' => 'Tents & Chairs', 'icon' => 'chair', 'description' => 'Vip tents, seating, decor, and shading for funeral grounds.'],
            ['name' => 'Public Address Systems', 'icon' => 'campaign', 'description' => 'Quality PA sound systems, microphones, and audio visual setups.'],
            ['name' => 'Transport', 'icon' => 'directions_bus', 'description' => 'Family bus transport, guest shuttles, and inter-county travel coordination.'],
            ['name' => 'Funeral Programme Printing', 'icon' => 'print', 'description' => 'Custom funeral booklets, eulogy booklets, and memorial cards.'],
            ['name' => 'Mourning Attire', 'icon' => 'styler', 'description' => 'Family uniforms, black mourning attire, and custom printed t-shirts.'],
            ['name' => 'Monument & Tombstones', 'icon' => 'monument', 'description' => 'Marble tombstones, granite grave markers, and headstone carving.'],
            ['name' => 'Funeral Insurance', 'icon' => 'security', 'description' => 'Last expense insurance covers and family funeral protection plans.'],
            ['name' => 'Other Funeral Services', 'icon' => 'more_horiz', 'description' => 'General funeral support, legal paperwork, and specialized services.'],
        ];

        foreach ($categories as $index => $cat) {
            BusinessCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                    'status' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        // 2. Banner Sizes
        $bannerSizesData = [
            ['name' => 'Horizontal Standard (765x95)', 'slug' => 'horizontal-765x95', 'width' => 765, 'height' => 95, 'type' => 'horizontal', 'max_size_kb' => 2048],
            ['name' => 'Horizontal Wide (770x195)', 'slug' => 'horizontal-770x195', 'width' => 770, 'height' => 195, 'type' => 'horizontal', 'max_size_kb' => 2048],
            ['name' => 'Horizontal Leaderboard (770x90)', 'slug' => 'horizontal-770x90', 'width' => 770, 'height' => 90, 'type' => 'horizontal', 'max_size_kb' => 2048],
            ['name' => 'Horizontal Banner (640x210)', 'slug' => 'horizontal-640x210', 'width' => 640, 'height' => 210, 'type' => 'horizontal', 'max_size_kb' => 2048],
            ['name' => 'Horizontal Compact (615x160)', 'slug' => 'horizontal-615x160', 'width' => 615, 'height' => 160, 'type' => 'horizontal', 'max_size_kb' => 2048],
            ['name' => 'Sidebar Tall (192x385)', 'slug' => 'sidebar-192x385', 'width' => 192, 'height' => 385, 'type' => 'sidebar', 'max_size_kb' => 2048],
            ['name' => 'Sidebar Medium (215x171)', 'slug' => 'sidebar-215x171', 'width' => 215, 'height' => 171, 'type' => 'sidebar', 'max_size_kb' => 2048],
            ['name' => 'Sidebar Square (160x165)', 'slug' => 'sidebar-160x165', 'width' => 160, 'height' => 165, 'type' => 'sidebar', 'max_size_kb' => 2048],
        ];

        $createdSizes = [];
        foreach ($bannerSizesData as $size) {
            $createdSizes[$size['slug']] = BannerSize::firstOrCreate(
                ['slug' => $size['slug']],
                [
                    'name' => $size['name'],
                    'width' => $size['width'],
                    'height' => $size['height'],
                    'type' => $size['type'],
                    'max_size_kb' => $size['max_size_kb'],
                    'status' => true,
                ]
            );
        }

        // 3. Ad Placements
        $placementsData = [
            ['name' => 'Homepage Header Banner', 'slug' => 'homepage_header', 'page_type' => 'homepage', 'description' => 'Top banner at the very top of the homepage.'],
            ['name' => 'Homepage After Hero', 'slug' => 'homepage_after_hero', 'page_type' => 'homepage', 'description' => 'Prime spot directly below the main homepage hero search.'],
            ['name' => 'Homepage Between Sections', 'slug' => 'homepage_between_sections', 'page_type' => 'homepage', 'description' => 'Banner rendered between homepage tribute categories.'],
            ['name' => 'Homepage Footer Banner', 'slug' => 'homepage_footer', 'page_type' => 'homepage', 'description' => 'Footer area banner at bottom of homepage.'],
            ['name' => 'Obituary Page Top Banner', 'slug' => 'obituary_top', 'page_type' => 'obituary', 'description' => 'Top banner on individual obituary detail pages.'],
            ['name' => 'Obituary Page Middle Banner', 'slug' => 'obituary_middle', 'page_type' => 'obituary', 'description' => 'Banner placed inside the biography & funeral service details.'],
            ['name' => 'Obituary Page Bottom Banner', 'slug' => 'obituary_bottom', 'page_type' => 'obituary', 'description' => 'Bottom banner right above candles & tribute comments.'],
            ['name' => 'Desktop Sidebar Card', 'slug' => 'desktop_sidebar', 'page_type' => 'sidebar', 'description' => 'Sticky sidebar ad card on desktop layout.'],
            ['name' => 'Search Results Between Results', 'slug' => 'search_between', 'page_type' => 'search', 'description' => 'Interspersed banner inside obituary search results.'],
            ['name' => 'Category Pages Banner', 'slug' => 'category_banner', 'page_type' => 'category', 'description' => 'Top banner on notice category landing pages.'],
            ['name' => 'County Pages Banner', 'slug' => 'county_banner', 'page_type' => 'county', 'description' => 'Targeted county banner on county landing pages.'],
        ];

        foreach ($placementsData as $p) {
            $placement = AdPlacement::firstOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'page_type' => $p['page_type'],
                    'description' => $p['description'],
                    'status' => true,
                ]
            );

            // Attach banner sizes
            if ($p['page_type'] === 'sidebar') {
                $placement->bannerSizes()->syncWithoutDetaching([
                    $createdSizes['sidebar-192x385']->id,
                    $createdSizes['sidebar-215x171']->id,
                    $createdSizes['sidebar-160x165']->id,
                ]);
            } else {
                $placement->bannerSizes()->syncWithoutDetaching([
                    $createdSizes['horizontal-765x95']->id,
                    $createdSizes['horizontal-770x195']->id,
                    $createdSizes['horizontal-770x90']->id,
                    $createdSizes['horizontal-640x210']->id,
                    $createdSizes['horizontal-615x160']->id,
                ]);
            }
        }

        // 4. Default Ad Pricing
        $allPlacements = AdPlacement::with('bannerSizes')->get();
        foreach ($allPlacements as $placement) {
            foreach ($placement->bannerSizes as $size) {
                AdPricing::firstOrCreate(
                    [
                        'ad_placement_id' => $placement->id,
                        'banner_size_id' => $size->id,
                    ],
                    [
                        'daily_rate' => $placement->page_type === 'sidebar' ? 400.00 : 600.00,
                        'national_daily_rate' => $placement->page_type === 'sidebar' ? 900.00 : 1500.00,
                        'featured_sur_charge' => 300.00,
                        'status' => true,
                    ]
                );
            }
        }

        // 5. Demo Advertiser & Business Profile
        $advertiser = Advertiser::firstOrCreate(
            ['email' => 'advertiser@obituaries.co.ke'],
            [
                'business_name' => 'Lee Funeral Home & Hearse Services',
                'contact_person' => 'James Lee',
                'phone_number' => '0722112233',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        $funeralHomeCat = BusinessCategory::where('slug', 'funeral-homes')->first();

        $profile = BusinessProfile::firstOrCreate(
            ['advertiser_id' => $advertiser->id],
            [
                'business_category_id' => $funeralHomeCat?->id,
                'business_name' => 'Lee Funeral Home & Hearse Services',
                'phone' => '0722112233',
                'whatsapp' => '254722112233',
                'email' => 'info@leefuneral.co.ke',
                'website' => 'https://leefuneral.co.ke',
                'google_maps_link' => 'https://maps.google.com/?q=Lee+Funeral+Home+Nairobi',
                'county' => 'Nairobi',
                'address' => 'Argwings Kodhek Road, Opposite KNH, Nairobi',
                'description' => 'Kenya’s premier funeral services provider. We offer 24/7 repatriation, body preservation, executive hearse motorcade, and casket services.',
                'status' => 'active',
            ]
        );

        // 6. Demo Active Ad Campaign
        $afterHeroPlacement = AdPlacement::where('slug', 'homepage_after_hero')->first();
        $bannerSize770x195 = BannerSize::where('slug', 'horizontal-770x195')->first();

        if ($afterHeroPlacement && $bannerSize770x195) {
            $campaign = AdCampaign::firstOrCreate(
                ['name' => 'Lee Funeral Home Premier Care Campaign'],
                [
                    'advertiser_id' => $advertiser->id,
                    'business_profile_id' => $profile->id,
                    'ad_placement_id' => $afterHeroPlacement->id,
                    'banner_size_id' => $bannerSize770x195->id,
                    'business_category_id' => $funeralHomeCat?->id,
                    'banner_path' => 'advertising/demo-banner.jpg',
                    'banner_webp_path' => 'advertising/demo-banner.webp',
                    'thumbnail_path' => 'advertising/demo-banner-thumb.jpg',
                    'landing_url' => 'https://leefuneral.co.ke',
                    'start_date' => now()->subDays(2)->format('Y-m-d'),
                    'end_date' => now()->addDays(28)->format('Y-m-d'),
                    'total_days' => 30,
                    'is_national' => true,
                    'is_featured' => true,
                    'calculated_price' => 36000.00,
                    'status' => 'running',
                    'approved_at' => now(),
                ]
            );

            AdCampaignCounty::firstOrCreate([
                'ad_campaign_id' => $campaign->id,
                'county' => 'Nairobi',
            ]);
            AdCampaignCounty::firstOrCreate([
                'ad_campaign_id' => $campaign->id,
                'county' => 'Kiambu',
            ]);
            AdCampaignCounty::firstOrCreate([
                'ad_campaign_id' => $campaign->id,
                'county' => 'Nakuru',
            ]);
        }
    }
}
