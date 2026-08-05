<?php

namespace App\Http\Controllers\Advertiser\Auth;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use App\Models\SecurityLog;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('advertiser.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:advertisers,email'],
        ], [
            'email.exists' => 'No advertiser account found with that email address.',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = route('advertiser.password.reset', ['token' => $token, 'email' => $email]);

        // Dispatch Branded Email via SMTP
        try {
            $advertiser = Advertiser::where('email', $email)->first();
            $name = $advertiser ? $advertiser->contact_person : 'Advertiser';

            $bodyContent = "<p>Dear {$name},</p>
            <p>You requested a password reset for your Obituaries.co.ke Advertiser Account.</p>
            <p>Click the button below to set a new password for your business account:</p>";

            MailService::sendEmail(
                $email,
                'Obituaries.co.ke Advertiser Password Reset Request',
                $bodyContent,
                $resetUrl,
                'Reset Advertiser Password'
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Advertiser Password Reset Mail Error: " . $e->getMessage());
        }

        return back()->with('success', "A password reset link has been sent to {$email}. Please check your inbox and spam folder.");
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->input('email');
        return view('advertiser.auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:advertisers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $advertiser = Advertiser::where('email', $email)->first();
        if ($advertiser) {
            $advertiser->password = Hash::make($request->password);
            $advertiser->save();
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        SecurityLog::log('advertiser_password_reset', 'info', null, "Advertiser password reset successfully for: {$email}");

        return redirect()->route('advertiser.login')->with('success', 'Your password has been reset successfully! You can now log in to your Advertiser Portal.');
    }
}
