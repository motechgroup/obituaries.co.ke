<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdCampaignCounty;
use App\Models\AdPlacement;
use App\Models\Advertiser;
use App\Models\BannerSize;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;
use App\Services\AdImageService;
use App\Services\AdPricingEngine;
use App\Services\MailService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCampaignController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = AdCampaign::with(['advertiser', 'businessProfile', 'placement', 'bannerSize'])
            ->withCount(['impressions', 'clicks']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $campaigns = $query->latest()->paginate(20);

        $counts = [
            'all' => AdCampaign::count(),
            'pending_approval' => AdCampaign::where('status', 'pending_approval')->count(),
            'running' => AdCampaign::where('status', 'running')->count(),
            'payment_pending' => AdCampaign::where('status', 'payment_pending')->count(),
            'rejected' => AdCampaign::where('status', 'rejected')->count(),
            'expired' => AdCampaign::where('status', 'expired')->count(),
        ];

        return view('admin.advertising.campaigns.index', compact('campaigns', 'status', 'counts'));
    }

    public function create()
    {
        $systemAdvertiser = Advertiser::firstOrCreate(
            ['email' => 'admin@obituaries.co.ke'],
            [
                'business_name' => 'System (Obituaries.co.ke Admin Direct Ads)',
                'contact_person' => 'System Administrator',
                'phone_number' => '0700000000',
                'password' => bcrypt('password123'),
                'status' => 'active',
            ]
        );

        $systemAdvertiser->businessProfile()->firstOrCreate(
            ['advertiser_id' => $systemAdvertiser->id],
            [
                'business_name' => 'System (Obituaries.co.ke Admin Direct Ads)',
                'phone' => '0700000000',
                'email' => 'admin@obituaries.co.ke',
            ]
        );

        $otherAdvertisers = Advertiser::with('businessProfile')
            ->where('id', '!=', $systemAdvertiser->id)
            ->orderBy('business_name')
            ->get();

        $advertisers = collect([$systemAdvertiser])->concat($otherAdvertisers);

        $placements = AdPlacement::with('bannerSizes')->where('status', true)->get();
        $bannerSizes = BannerSize::where('status', true)->get();
        $categories = BusinessCategory::where('status', true)->orderBy('name')->get();

        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        return view('admin.advertising.campaigns.create', compact(
            'advertisers',
            'placements',
            'bannerSizes',
            'categories',
            'counties'
        ));
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'advertiser_id' => ['required', 'exists:advertisers,id'],
            'name' => ['required', 'string', 'max:255'],
            'ad_placement_id' => ['required', 'exists:ad_placements,id'],
            'banner_size_id' => ['required', 'exists:banner_sizes,id'],
            'business_category_id' => ['nullable', 'exists:business_categories,id'],
            'landing_url' => ['nullable', 'url', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_national' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'counties' => ['nullable', 'array'],
            'counties.*' => ['string'],
            'banner_image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'status' => ['required', 'string', 'in:running,pending_approval,draft'],
            'calculated_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $advertiser = Advertiser::findOrFail($validated['advertiser_id']);
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id], [
            'business_name' => $advertiser->business_name,
            'phone' => $advertiser->phone_number,
            'email' => $advertiser->email,
        ]);

        $bannerSize = BannerSize::findOrFail($validated['banner_size_id']);

        // 1. Validate image
        $validationResult = AdImageService::validateBanner($request->file('banner_image'), $bannerSize);
        if (!$validationResult['valid']) {
            return back()->withInput()->withErrors(['banner_image' => implode(' ', $validationResult['errors'])]);
        }

        // 2. Process image
        $processedImages = AdImageService::processAndSaveBanner($request->file('banner_image'), $bannerSize);

        // 3. Price calculation
        $isNational = (bool) ($validated['is_national'] ?? false);
        $isFeatured = (bool) ($validated['is_featured'] ?? false);
        $counties = $validated['counties'] ?? [];

        $pricingResult = AdPricingEngine::calculatePrice(
            (int) $validated['ad_placement_id'],
            (int) $validated['banner_size_id'],
            $validated['start_date'],
            $validated['end_date'],
            $counties,
            $isNational,
            $isFeatured
        );

        $price = $validated['calculated_price'] ?? $pricingResult['total_price'];

        // 4. Create campaign
        $campaign = AdCampaign::create([
            'advertiser_id' => $advertiser->id,
            'business_profile_id' => $profile->id,
            'ad_placement_id' => $validated['ad_placement_id'],
            'banner_size_id' => $validated['banner_size_id'],
            'business_category_id' => $validated['business_category_id'] ?? $profile->business_category_id,
            'name' => $validated['name'],
            'banner_path' => $processedImages['banner_path'],
            'banner_webp_path' => $processedImages['banner_webp_path'],
            'thumbnail_path' => $processedImages['thumbnail_path'],
            'landing_url' => $validated['landing_url'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $pricingResult['total_days'],
            'is_national' => $isNational,
            'is_featured' => $isFeatured,
            'calculated_price' => $price,
            'status' => $validated['status'],
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        if (!$isNational && !empty($counties)) {
            foreach ($counties as $c) {
                AdCampaignCounty::create([
                    'ad_campaign_id' => $campaign->id,
                    'county' => $c,
                ]);
            }
        }

        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', "Ad campaign '{$campaign->name}' created and placed successfully!");
    }

    public function edit(AdCampaign $campaign)
    {
        $campaign->load(['advertiser', 'businessProfile', 'placement', 'bannerSize', 'category', 'counties']);

        $systemAdvertiser = Advertiser::firstOrCreate(
            ['email' => 'admin@obituaries.co.ke'],
            [
                'business_name' => 'System (Obituaries.co.ke Admin Direct Ads)',
                'contact_person' => 'System Administrator',
                'phone_number' => '0700000000',
                'password' => bcrypt('password123'),
                'status' => 'active',
            ]
        );

        $otherAdvertisers = Advertiser::with('businessProfile')
            ->where('id', '!=', $systemAdvertiser->id)
            ->orderBy('business_name')
            ->get();

        $advertisers = collect([$systemAdvertiser])->concat($otherAdvertisers);

        $placements = AdPlacement::with('bannerSizes')->where('status', true)->get();
        $bannerSizes = BannerSize::where('status', true)->get();
        $categories = BusinessCategory::where('status', true)->orderBy('name')->get();

        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        return view('admin.advertising.campaigns.edit', compact(
            'campaign',
            'advertisers',
            'placements',
            'bannerSizes',
            'categories',
            'counties'
        ));
    }

    public function update(Request $request, AdCampaign $campaign)
    {
        $validated = $request->validate([
            'advertiser_id' => ['required', 'exists:advertisers,id'],
            'name' => ['required', 'string', 'max:255'],
            'ad_placement_id' => ['required', 'exists:ad_placements,id'],
            'banner_size_id' => ['required', 'exists:banner_sizes,id'],
            'business_category_id' => ['nullable', 'exists:business_categories,id'],
            'landing_url' => ['nullable', 'url', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_national' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'counties' => ['nullable', 'array'],
            'counties.*' => ['string'],
            'banner_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'status' => ['required', 'string', 'in:running,pending_approval,approved,payment_pending,rejected,expired,draft'],
            'calculated_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $advertiser = Advertiser::findOrFail($validated['advertiser_id']);
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id], [
            'business_name' => $advertiser->business_name,
            'phone' => $advertiser->phone_number,
            'email' => $advertiser->email,
        ]);

        $bannerSize = BannerSize::findOrFail($validated['banner_size_id']);

        $updateData = [
            'advertiser_id' => $advertiser->id,
            'business_profile_id' => $profile->id,
            'ad_placement_id' => $validated['ad_placement_id'],
            'banner_size_id' => $validated['banner_size_id'],
            'business_category_id' => $validated['business_category_id'] ?? $profile->business_category_id,
            'name' => $validated['name'],
            'landing_url' => $validated['landing_url'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_national' => (bool) ($validated['is_national'] ?? false),
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'status' => $validated['status'],
        ];

        if (isset($validated['calculated_price'])) {
            $updateData['calculated_price'] = $validated['calculated_price'];
        }

        // Process new banner image if uploaded
        if ($request->hasFile('banner_image')) {
            $validationResult = AdImageService::validateBanner($request->file('banner_image'), $bannerSize);
            if (!$validationResult['valid']) {
                return back()->withInput()->withErrors(['banner_image' => implode(' ', $validationResult['errors'])]);
            }
            $processedImages = AdImageService::processAndSaveBanner($request->file('banner_image'), $bannerSize);
            $updateData['banner_path'] = $processedImages['banner_path'];
            $updateData['banner_webp_path'] = $processedImages['banner_webp_path'];
            $updateData['thumbnail_path'] = $processedImages['thumbnail_path'];
        }

        $campaign->update($updateData);

        // Sync counties
        $campaign->counties()->delete();
        if (!$updateData['is_national'] && !empty($validated['counties'])) {
            foreach ($validated['counties'] as $c) {
                AdCampaignCounty::create([
                    'ad_campaign_id' => $campaign->id,
                    'county' => $c,
                ]);
            }
        }

        return redirect()->route('admin.advertising.campaigns.show', $campaign->id)
            ->with('success', "Campaign '{$campaign->name}' updated successfully!");
    }

    public function show(AdCampaign $campaign)
    {
        $campaign->load(['advertiser', 'businessProfile', 'placement', 'bannerSize', 'category', 'counties', 'payments']);
        $campaign->loadCount(['impressions', 'clicks']);

        return view('admin.advertising.campaigns.show', compact('campaign'));
    }

    public function approve(AdCampaign $campaign)
    {
        $admin = Auth::guard('admin')->user();

        $campaign->update([
            'status' => 'running',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        // Send Notification Email & SMS
        $advertiser = $campaign->advertiser;
        if ($advertiser) {
            $msg = "Dear {$advertiser->contact_person}, your ad campaign '{$campaign->name}' on Obituaries.co.ke has been APPROVED and is now live!";
            SmsService::sendSms($advertiser->phone_number, $msg);
            MailService::sendEmail($advertiser->email, "Ad Campaign Approved: {$campaign->name}", $msg);
        }

        return back()->with('success', "Campaign '{$campaign->name}' has been APPROVED and is now live!");
    }

    public function reject(Request $request, AdCampaign $campaign)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $campaign->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $advertiser = $campaign->advertiser;
        if ($advertiser) {
            $msg = "Dear {$advertiser->contact_person}, your ad campaign '{$campaign->name}' was not approved. Reason: {$validated['rejection_reason']}";
            SmsService::sendSms($advertiser->phone_number, $msg);
            MailService::sendEmail($advertiser->email, "Ad Campaign Update: {$campaign->name}", $msg);
        }

        return back()->with('success', "Campaign '{$campaign->name}' has been REJECTED.");
    }

    public function pause(AdCampaign $campaign)
    {
        $campaign->update(['status' => 'approved']);
        return back()->with('success', "Campaign '{$campaign->name}' has been PAUSED.");
    }

    public function resume(AdCampaign $campaign)
    {
        $campaign->update(['status' => 'running']);
        return back()->with('success', "Campaign '{$campaign->name}' has been RESUMED.");
    }

    public function destroy(AdCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
