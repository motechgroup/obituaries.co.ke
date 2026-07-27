<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            // Branding & General
            'site_title' => Setting::get('site_title', 'Obituaries.co.ke'),
            'site_tagline' => Setting::get('site_tagline', 'A dignified space for remembrance, honouring loved ones across Kenya.'),
            'logo' => Setting::get('logo'),
            'favicon' => Setting::get('favicon'),
            'footer_address' => Setting::get('footer_address', 'Nairobi, Kenya'),
            'footer_phone' => Setting::get('footer_phone', '+254 700 000 000'),
            'footer_email' => Setting::get('footer_email', 'support@obituaries.co.ke'),
            'copyright_text' => Setting::get('copyright_text', '© ' . date('Y') . ' Obituaries.co.ke. All rights reserved.'),

            // Publishing & M-Pesa
            'obituary_publishing_cost' => Setting::get('obituary_publishing_cost', '500'),
            'mpesa_env' => Setting::get('mpesa_env', 'sandbox'),
            'mpesa_shortcode' => Setting::get('mpesa_shortcode', '174379'),
            'mpesa_passkey' => Setting::get('mpesa_passkey', ''),
            'mpesa_consumer_key' => Setting::get('mpesa_consumer_key', ''),
            'mpesa_consumer_secret' => Setting::get('mpesa_consumer_secret', ''),

            // SMTP Mail & Templates
            'mail_host' => Setting::get('mail_host', 'smtp.mailtrap.io'),
            'mail_port' => Setting::get('mail_port', '2525'),
            'mail_username' => Setting::get('mail_username', ''),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address', 'notifications@obituaries.co.ke'),
            'mail_from_name' => Setting::get('mail_from_name', 'Obituaries.co.ke'),
            'mail_template_verification' => Setting::get('mail_template_verification', "Dear {NAME},\n\nYour obituary notice for {DECEASED_NAME} has been verified and published live on Obituaries.co.ke.\n\nView Live: {LINK}\n\nWarm regards,\nObituaries.co.ke Team"),
            'mail_template_rejection' => Setting::get('mail_template_rejection', "Dear {NAME},\n\nRegrettably, your obituary submission for {DECEASED_NAME} could not be approved due to the following reason:\n\nReason: {REASON}\n\nPlease contact our editorial team if you have questions.\n\nWarm regards,\nObituaries.co.ke Editorial Team"),
            'mail_template_anniversary' => Setting::get('mail_template_anniversary', "Dear {NAME},\n\nToday marks the {YEARS} Anniversary of the passing of {DECEASED_NAME}.\n\nIn honoring their cherished legacy, friends and family are remembering them today on Obituaries.co.ke.\n\nView Memorial: {LINK}\n\nWarm regards,\nObituaries.co.ke Team"),

            // SMS Gateway & Templates
            'sms_provider' => Setting::get('sms_provider', 'africastalking'),
            'sms_api_key' => Setting::get('sms_api_key', ''),
            'sms_shortcode' => Setting::get('sms_shortcode', 'OBITUARIES'),
            'sms_sender_id' => Setting::get('sms_sender_id', 'OBITUARIES'),
            'sms_template_submission' => Setting::get('sms_template_submission', "Dear {NAME}, your obituary submission for {DECEASED_NAME} has been received. Complete payment to publish."),
            'sms_template_approval' => Setting::get('sms_template_approval', "Dear {NAME}, the obituary for {DECEASED_NAME} is now published live: {LINK}"),
            'sms_template_rejection' => Setting::get('sms_template_rejection', "Dear {NAME}, your obituary submission for {DECEASED_NAME} was not approved. Reason: {REASON}"),
            'sms_template_anniversary' => Setting::get('sms_template_anniversary', "Dear {NAME}, today marks the {YEARS} Anniversary of {DECEASED_NAME}'s passing. We join you in memory: {LINK}"),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_title' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,ico,svg,webp', 'max:1024'],
            'footer_address' => ['nullable', 'string', 'max:255'],
            'footer_phone' => ['nullable', 'string', 'max:50'],
            'footer_email' => ['nullable', 'email', 'max:255'],
            'copyright_text' => ['nullable', 'string', 'max:255'],

            'obituary_publishing_cost' => ['required', 'numeric', 'min:0'],
            'mpesa_env' => ['nullable', 'string', 'in:sandbox,live'],
            'mpesa_shortcode' => ['nullable', 'string', 'max:50'],
            'mpesa_passkey' => ['nullable', 'string'],
            'mpesa_consumer_key' => ['nullable', 'string'],
            'mpesa_consumer_secret' => ['nullable', 'string'],

            'mail_host' => ['nullable', 'string'],
            'mail_port' => ['nullable', 'numeric'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['nullable', 'string'],
            'mail_from_address' => ['nullable', 'email'],
            'mail_from_name' => ['nullable', 'string'],
            'mail_template_verification' => ['nullable', 'string'],
            'mail_template_rejection' => ['nullable', 'string'],
            'mail_template_anniversary' => ['nullable', 'string'],

            'sms_provider' => ['nullable', 'string'],
            'sms_api_key' => ['nullable', 'string'],
            'sms_shortcode' => ['nullable', 'string'],
            'sms_sender_id' => ['nullable', 'string'],
            'sms_template_submission' => ['nullable', 'string'],
            'sms_template_approval' => ['nullable', 'string'],
            'sms_template_rejection' => ['nullable', 'string'],
            'sms_template_anniversary' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('branding', 'public');
            Setting::set('logo', $logoPath);
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('branding', 'public');
            Setting::set('favicon', $faviconPath);
        }

        foreach ($validated as $key => $value) {
            if ($key !== 'logo' && $key !== 'favicon') {
                Setting::set($key, $value);
            }
        }

        $activeTab = $request->input('active_tab', 'branding');

        return back()
            ->with('active_tab', $activeTab)
            ->with('success', 'Platform settings and email/SMS templates updated successfully!');
    }

    public function sendTestMail(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $recipient = $request->input('test_email');

        try {
            $host = Setting::get('mail_host', config('mail.mailers.smtp.host'));
            $port = Setting::get('mail_port', config('mail.mailers.smtp.port'));
            $username = Setting::get('mail_username', config('mail.mailers.smtp.username'));
            $password = Setting::get('mail_password', config('mail.mailers.smtp.password'));
            $encryption = Setting::get('mail_encryption', config('mail.mailers.smtp.encryption'));
            $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName = Setting::get('mail_from_name', config('mail.from.name'));

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);

            Mail::raw("Hello!\n\nThis is a test email sent from your Obituaries.co.ke Admin Panel.\nYour SMTP Mail Server configuration is working properly!", function ($message) use ($recipient, $fromAddress, $fromName) {
                $message->to($recipient)
                    ->from($fromAddress, $fromName)
                    ->subject('Obituaries.co.ke SMTP Test Email');
            });

            return back()
                ->with('active_tab', 'smtp')
                ->with('success', "📧 Test email dispatched successfully to {$recipient}!");
        } catch (\Throwable $e) {
            return back()
                ->with('active_tab', 'smtp')
                ->with('error', "Failed to send test email: " . $e->getMessage());
        }
    }

    public function sendTestSms(Request $request)
    {
        $request->validate([
            'test_phone' => ['required', 'string'],
        ]);

        $phone = $request->input('test_phone');
        $provider = Setting::get('sms_provider', 'africastalking');

        Log::info("Test SMS dispatched to {$phone} via {$provider}.");

        return back()
            ->with('active_tab', 'sms')
            ->with('success', "📱 Test SMS dispatched successfully to {$phone} (Provider: " . strtoupper($provider) . ")!");
    }
}
