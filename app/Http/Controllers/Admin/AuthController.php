<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SecurityLog;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        // 1. Sanitize input
        $email = strtolower(trim((string) $request->input('email')));

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = $email;

        // 2. Attempt authentication
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            SecurityLog::log('admin_login', 'info', null, "Admin user " . Auth::guard('admin')->user()->name . " logged in successfully.");
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, Administrator!');
        }

        SecurityLog::log('admin_login_failed', 'warning', null, "Failed login attempt for admin email: {$email}");

        return back()->withErrors([
            'email' => 'The provided credentials do not match our administrative records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // Sanitize
        $email = strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:admins,email'],
        ], [
            'email.exists' => 'No administrative account found with that email address.',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = route('admin.password.reset', ['token' => $token, 'email' => $email]);

        // Send Branded SMTP Email
        try {
            $bodyContent = "<p>Hello,</p>
            <p>You requested a password reset for your Obituaries.co.ke Administrative Account.</p>
            <p>Click the button below to set a new secure password for your account:</p>";

            MailService::sendEmail(
                $email,
                'Obituaries.co.ke Admin Password Reset Request',
                $bodyContent,
                $resetUrl,
                'Reset Admin Password'
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Admin Password Reset Mail Error: " . $e->getMessage());
        }

        return back()->with('success', "A password reset link has been sent to {$email}. Please check your inbox (and spam folder).");
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->input('email');
        return view('admin.auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $admin = Admin::where('email', $email)->first();
        if ($admin) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        SecurityLog::log('admin_password_reset', 'info', null, "Admin password reset successfully for: {$email}");

        return redirect()->route('admin.login')->with('success', 'Your password has been reset successfully! You can now log in.');
    }
}
