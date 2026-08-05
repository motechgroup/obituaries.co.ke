<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use App\Models\AdPricing;
use App\Models\BannerSize;
use Illuminate\Http\Request;

class AdminPricingController extends Controller
{
    public function index()
    {
        $pricings = AdPricing::with(['placement', 'bannerSize'])->latest()->paginate(20);
        $placements = AdPlacement::with('bannerSizes')->where('status', true)->get();
        $bannerSizes = BannerSize::where('status', true)->get();

        return view('admin.advertising.pricing.index', compact('pricings', 'placements', 'bannerSizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_placement_id' => ['required', 'exists:ad_placements,id'],
            'banner_size_id' => ['required', 'exists:banner_sizes,id'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'national_daily_rate' => ['required', 'numeric', 'min:0'],
            'featured_sur_charge' => ['required', 'numeric', 'min:0'],
        ]);

        AdPricing::updateOrCreate(
            [
                'ad_placement_id' => $validated['ad_placement_id'],
                'banner_size_id' => $validated['banner_size_id'],
            ],
            [
                'daily_rate' => $validated['daily_rate'],
                'national_daily_rate' => $validated['national_daily_rate'],
                'featured_sur_charge' => $validated['featured_sur_charge'],
                'status' => true,
            ]
        );

        return back()->with('success', 'Ad pricing rate updated successfully.');
    }

    public function update(Request $request, AdPricing $pricing)
    {
        $validated = $request->validate([
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'national_daily_rate' => ['required', 'numeric', 'min:0'],
            'featured_sur_charge' => ['required', 'numeric', 'min:0'],
        ]);

        $pricing->update($validated);
        return back()->with('success', 'Ad pricing rate updated successfully.');
    }
}
