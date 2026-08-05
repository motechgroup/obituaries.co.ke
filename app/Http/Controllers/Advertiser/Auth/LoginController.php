<?php

namespace App\Http\Controllers\Advertiser\Auth;

use App\Http\Controllers\Controller;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('advertiser')->attempt($credentials, $remember)) {
            $user = Auth::guard('advertiser')->user();

            if ($user->status !== 'active') {
                Auth::guard('advertiser')->logout();
                return back()->withErrors(['email' => 'Your advertiser account is currently suspended. Please contact support.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('advertiser.dashboard'));
        }

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
