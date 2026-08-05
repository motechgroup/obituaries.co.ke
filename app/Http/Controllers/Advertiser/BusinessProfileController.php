<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $advertiser = Auth::guard('advertiser')->user();
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id], [
            'business_name' => $advertiser->business_name,
            'phone' => $advertiser->phone_number,
            'email' => $advertiser->email,
        ]);

        $categories = BusinessCategory::where('status', true)->orderBy('name')->get();
        $counties = [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo Marakwet', 'Embu', 'Garissa', 'Homa Bay',
            'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii',
            'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera',
            'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi', 'Nakuru', 'Nandi',
            'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya', 'Taita Taveta', 'Tana River',
            'Tharaka Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot'
        ];

        return view('advertiser.profile.edit', compact('advertiser', 'profile', 'categories', 'counties'));
    }

    public function update(Request $request)
    {
        $advertiser = Auth::guard('advertiser')->user();
        $profile = BusinessProfile::firstOrCreate(['advertiser_id' => $advertiser->id]);

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_category_id' => ['required', 'exists:business_categories,id'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'google_maps_link' => ['nullable', 'string', 'max:1000'],
            'county' => ['required', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = StorageHelper::savePublicFile($request->file('logo'), 'advertising/logos');
        }

        $profile->update($validated);
        $advertiser->update(['business_name' => $validated['business_name'], 'phone_number' => $validated['phone']]);

        return back()->with('success', 'Your business profile details have been updated successfully!');
    }
}
