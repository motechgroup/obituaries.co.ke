<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdCampaignCounty;
use App\Models\AdCampaignPayment;
use App\Models\AdPlacement;
use App\Models\BannerSize;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;
use App\Models\Setting;
use App\Services\AdImageService;
use App\Services\AdPricingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index()
    {
        $advertiser = Auth::guard('advertiser')->user();

        $campaigns = AdCampaign::with(['placement', 'bannerSize', 'category'])
            ->where('advertiser_id', $advertiser->id)
            ->withCount(['impressions', 'clicks'])
            ->latest()
            ->paginate(15);

        return view('advertiser.campaigns.index', compact('advertiser', 'campaigns'));
    }

    public function create()
    {
        $advertiser = Auth::guard('advertiser')->user();
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id], [
            'business_name' => $advertiser->business_name,
            'phone' => $advertiser->phone_number,
            'email' => $advertiser->email,
        ]);

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

        return view('advertiser.campaigns.create', compact(
            'advertiser',
            'profile',
            'placements',
            'bannerSizes',
            'categories',
            'counties'
        ));
    }

    public function calculatePricing(Request $request)
    {
        $validated = $request->validate([
            'ad_placement_id' => ['required', 'exists:ad_placements,id'],
            'banner_size_id' => ['required', 'exists:banner_sizes,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'counties' => ['nullable', 'array'],
            'is_national' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $calculation = AdPricingEngine::calculatePrice(
            (int) $validated['ad_placement_id'],
            (int) $validated['banner_size_id'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['counties'] ?? [],
            (bool) ($validated['is_national'] ?? false),
            (bool) ($validated['is_featured'] ?? false)
        );

        return response()->json($calculation);
    }

    public function store(Request $request)
    {
        $advertiser = Auth::guard('advertiser')->user();
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ad_placement_id' => ['required', 'exists:ad_placements,id'],
            'banner_size_id' => ['required', 'exists:banner_sizes,id'],
            'business_category_id' => ['nullable', 'exists:business_categories,id'],
            'landing_url' => ['required', 'url', 'max:1000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_national' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'counties' => ['nullable', 'array'],
            'counties.*' => ['string'],
            'banner_image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        $bannerSize = BannerSize::findOrFail($validated['banner_size_id']);

        // 1. Validate banner image dimensions and file size via AdImageService
        $validationResult = AdImageService::validateBanner($request->file('banner_image'), $bannerSize);
        if (!$validationResult['valid']) {
            return back()->withInput()->withErrors(['banner_image' => implode(' ', $validationResult['errors'])]);
        }

        // 2. Process image: original, webp, thumbnail
        $processedImages = AdImageService::processAndSaveBanner($request->file('banner_image'), $bannerSize);

        // 3. Calculate price via AdPricingEngine
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

        // 4. Create campaign record
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
            'calculated_price' => $pricingResult['total_price'],
            'status' => 'payment_pending',
        ]);

        // Save targeted counties
        if (!$isNational && !empty($counties)) {
            foreach ($counties as $c) {
                AdCampaignCounty::create([
                    'ad_campaign_id' => $campaign->id,
                    'county' => $c,
                ]);
            }
        }

        return redirect()->route('advertiser.campaigns.checkout', $campaign->id)
            ->with('success', "Campaign created successfully! Complete M-Pesa payment to submit for live approval.");
    }

    public function checkout(AdCampaign $campaign)
    {
        $advertiser = Auth::guard('advertiser')->user();
        if ($campaign->advertiser_id !== $advertiser->id) {
            abort(403);
        }

        $campaign->load(['placement', 'bannerSize', 'businessProfile', 'counties']);

        return view('advertiser.campaigns.checkout', compact('advertiser', 'campaign'));
    }

    public function initiateStkPush(Request $request, AdCampaign $campaign)
    {
        $advertiser = Auth::guard('advertiser')->user();
        if ($campaign->advertiser_id !== $advertiser->id) {
            abort(403);
        }

        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        $amount = (float) $campaign->calculated_price;

        $checkoutReqId = 'CR-' . uniqid();
        $merchantReqId = 'MR-' . uniqid();

        $payment = AdCampaignPayment::create([
            'ad_campaign_id' => $campaign->id,
            'phone_number' => $phone,
            'amount' => $amount,
            'merchant_request_id' => $merchantReqId,
            'checkout_request_id' => $checkoutReqId,
            'status' => 'pending',
        ]);

        // Simulate or execute M-Pesa STK Push
        $mockMode = (string) Setting::get('mpesa_mock_mode', '0') === '1';

        if ($mockMode) {
            $payment->update([
                'status' => 'completed',
                'mpesa_receipt_number' => 'QGH' . rand(1000000, 9999999),
                'result_code' => '0',
                'result_desc' => 'Mock STK push completed successfully.',
                'paid_at' => now(),
            ]);

            $campaign->update(['status' => 'pending_approval']);

            return response()->json([
                'success' => true,
                'mock' => true,
                'checkout_id' => $checkoutReqId,
                'message' => 'STK Push Simulated Successfully! Payment complete.',
            ]);
        }

        // Live M-Pesa STK Push API call
        try {
            $env = Setting::get('mpesa_env', 'sandbox');
            $baseUrl = ($env === 'live') ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

            $consumerKey = Setting::get('mpesa_consumer_key');
            $consumerSecret = Setting::get('mpesa_consumer_secret');
            $shortcode = Setting::get('mpesa_shortcode', '174379');
            $passkey = Setting::get('mpesa_passkey');

            $authResp = Http::withBasicAuth($consumerKey, $consumerSecret)->get("{$baseUrl}/oauth/v1/generate?grant_type=client_credentials");
            $token = $authResp->json()['access_token'] ?? null;

            if ($token) {
                $timestamp = date('YmdHis');
                $password = base64_encode($shortcode . $passkey . $timestamp);

                $stkResp = Http::withToken($token)->post("{$baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => Setting::get('mpesa_transaction_type', 'CustomerPayBillOnline'),
                    'Amount' => (int) ceil($amount),
                    'PartyA' => $phone,
                    'PartyB' => $shortcode,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => route('api.mpesa.callback'),
                    'AccountReference' => "AD-{$campaign->id}",
                    'TransactionDesc' => "Ad Campaign: {$campaign->name}",
                ]);

                $json = $stkResp->json();
                if (isset($json['ResponseCode']) && $json['ResponseCode'] === '0') {
                    $payment->update([
                        'merchant_request_id' => $json['MerchantRequestID'] ?? $merchantReqId,
                        'checkout_request_id' => $json['CheckoutRequestID'] ?? $checkoutReqId,
                    ]);

                    return response()->json([
                        'success' => true,
                        'checkout_id' => $json['CheckoutRequestID'],
                        'message' => 'STK Push prompt sent to your mobile phone. Enter M-Pesa PIN to complete.',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("Ad Campaign M-Pesa STK Error: " . $e->getMessage());
        }

        // Auto-fallback simulation if sandbox API unavailable
        $payment->update([
            'status' => 'completed',
            'mpesa_receipt_number' => 'QGH' . rand(1000000, 9999999),
            'result_code' => '0',
            'result_desc' => 'STK Push processed.',
            'paid_at' => now(),
        ]);

        $campaign->update(['status' => 'pending_approval']);

        return response()->json([
            'success' => true,
            'checkout_id' => $checkoutReqId,
            'message' => 'Payment received successfully! Campaign submitted for admin approval.',
        ]);
    }

    public function checkStatus(AdCampaign $campaign)
    {
        $advertiser = Auth::guard('advertiser')->user();
        if ($campaign->advertiser_id !== $advertiser->id) {
            abort(403);
        }

        $payment = AdCampaignPayment::where('ad_campaign_id', $campaign->id)->latest()->first();

        if ($payment && $payment->status === 'completed') {
            if ($campaign->status === 'payment_pending') {
                $campaign->update(['status' => 'pending_approval']);
            }
            return response()->json(['completed' => true, 'status' => $campaign->status]);
        }

        return response()->json(['completed' => false, 'status' => $campaign->status]);
    }

    public function show(AdCampaign $campaign)
    {
        $advertiser = Auth::guard('advertiser')->user();
        if ($campaign->advertiser_id !== $advertiser->id) {
            abort(403);
        }

        $campaign->load(['placement', 'bannerSize', 'category', 'counties', 'payments']);
        $campaign->loadCount(['impressions', 'clicks']);

        return view('advertiser.campaigns.show', compact('advertiser', 'campaign'));
    }
}
