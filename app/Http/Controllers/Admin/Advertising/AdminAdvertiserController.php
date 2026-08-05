<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function create()
    {
        return view('admin.advertising.advertisers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:advertisers,email'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'string', 'in:active,suspended'],
        ]);

        $advertiser = Advertiser::create([
            'business_name' => $validated['business_name'],
            'contact_person' => $validated['contact_person'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'email_verified_at' => now(),
            'status' => $validated['status'],
        ]);

        BusinessProfile::create([
            'advertiser_id' => $advertiser->id,
            'business_name' => $advertiser->business_name,
            'phone' => $advertiser->phone_number,
            'email' => $advertiser->email,
            'status' => 'active',
        ]);

        return redirect()->route('admin.advertising.advertisers.index')
            ->with('success', "Advertiser '{$advertiser->business_name}' created successfully.");
    }

    public function show(Advertiser $advertiser)
    {
        $advertiser->load(['profiles.category', 'campaigns.placement', 'campaigns.bannerSize']);
        return view('admin.advertising.advertisers.show', compact('advertiser'));
    }

    public function edit(Advertiser $advertiser)
    {
        return view('admin.advertising.advertisers.edit', compact('advertiser'));
    }

    public function update(Request $request, Advertiser $advertiser)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('advertisers')->ignore($advertiser->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'string', 'in:active,suspended'],
        ]);

        $updateData = [
            'business_name' => $validated['business_name'],
            'contact_person' => $validated['contact_person'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $advertiser->update($updateData);

        return redirect()->route('admin.advertising.advertisers.index')
            ->with('success', "Advertiser '{$advertiser->business_name}' updated successfully.");
    }

    public function toggleStatus(Advertiser $advertiser)
    {
        $newStatus = $advertiser->status === 'active' ? 'suspended' : 'active';
        $advertiser->update(['status' => $newStatus]);

        return back()->with('success', "Advertiser account status updated to '{$newStatus}'.");
    }

    public function destroy(Advertiser $advertiser)
    {
        $activeCampaigns = $advertiser->campaigns()->whereIn('status', ['running', 'approved'])->count();
        if ($activeCampaigns > 0) {
            return back()->with('error', "Cannot delete advertiser '{$advertiser->business_name}' because they have {$activeCampaigns} active campaigns.");
        }

        $advertiser->delete();
        return redirect()->route('admin.advertising.advertisers.index')
            ->with('success', "Advertiser account deleted successfully.");
    }
}
