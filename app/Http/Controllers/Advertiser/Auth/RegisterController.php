<?php

namespace App\Http\Controllers\Advertiser\Auth;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::guard('advertiser')->check()) {
            return redirect()->route('advertiser.dashboard');
        }
        return view('advertiser.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:advertisers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $advertiser = Advertiser::create([
            'business_name' => $validated['business_name'],
            'contact_person' => $validated['contact_person'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // auto-verified for frictionless onboarding
            'status' => 'active',
        ]);

        // Create initial default business profile
        BusinessProfile::create([
            'advertiser_id' => $advertiser->id,
            'business_name' => $validated['business_name'],
            'phone' => $validated['phone_number'],
            'email' => $validated['email'],
            'status' => 'active',
        ]);

        Auth::guard('advertiser')->login($advertiser);

        return redirect()->route('advertiser.dashboard')
            ->with('success', "Welcome to Obituaries.co.ke Advertising Portal! Your advertiser account has been created.");
    }
}
