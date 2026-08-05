<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use Illuminate\Http\Request;

class AdminAdvertiserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Advertiser::with(['profiles', 'campaigns']);
        if (!empty($search)) {
            $query->where('business_name', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");
        }

        $advertisers = $query->latest()->paginate(20);

        return view('admin.advertising.advertisers.index', compact('advertisers', 'search'));
    }

    public function show(Advertiser $advertiser)
    {
        $advertiser->load(['profiles.category', 'campaigns.placement', 'campaigns.bannerSize']);
        return view('admin.advertising.advertisers.show', compact('advertiser'));
    }

    public function toggleStatus(Advertiser $advertiser)
    {
        $newStatus = $advertiser->status === 'active' ? 'suspended' : 'active';
        $advertiser->update(['status' => $newStatus]);

        return back()->with('success', "Advertiser account status updated to '{$newStatus}'.");
    }
}
