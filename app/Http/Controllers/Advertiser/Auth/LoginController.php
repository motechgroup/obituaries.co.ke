<?php

namespace App\Http\Controllers\Advertiser\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('advertiser')->check()) {
            return redirect()->route('advertiser.dashboard');
        }
        return view('advertiser.auth.login');
    }

    public function login(Request $request)
    {
        // 1. Sanitize email input
        $email = strtolower(trim((string) $request->input('email')));

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = $email;
        $remember = $request->boolean('remember');

        // 2. Attempt login
        if (Auth::guard('advertiser')->attempt($credentials, $remember)) {
            $user = Auth::guard('advertiser')->user();

            if ($user->status !== 'active') {
                Auth::guard('advertiser')->logout();
                SecurityLog::log('advertiser_login_suspended', 'warning', null, "Suspended advertiser attempted login: {$email}");
                return back()->withErrors(['email' => 'Your advertiser account is currently suspended. Please contact support.']);
            }

            $request->session()->regenerate();
            SecurityLog::log('advertiser_login', 'info', null, "Advertiser business '{$user->business_name}' logged in successfully.");
            return redirect()->intended(route('advertiser.dashboard'));
        }

        SecurityLog::log('advertiser_login_failed', 'warning', null, "Failed advertiser login attempt for email: {$email}");

        return back()->withErrors([
            'email' => 'The provided credentials do not match our advertiser records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('advertiser')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('advertiser.login')
            ->with('success', 'You have been logged out of the Advertiser Portal.');
    }
}
