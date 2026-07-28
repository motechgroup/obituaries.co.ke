<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            'auto_publish_obituaries' => Setting::get('auto_publish_obituaries', '0'),
            'show_poster_details' => Setting::get('show_poster_details', '0'),
            'mpesa_env' => Setting::get('mpesa_env', 'sandbox'),
            'mpesa_shortcode' => Setting::get('mpesa_shortcode', '174379'),
            'mpesa_passkey' => Setting::get('mpesa_passkey', ''),
            'mpesa_consumer_key' => Setting::get('mpesa_consumer_key', ''),
            'mpesa_consumer_secret' => Setting::get('mpesa_consumer_secret', ''),
            'mpesa_mock_mode' => Setting::get('mpesa_mock_mode', '0'),
            'mpesa_transaction_type' => Setting::get('mpesa_transaction_type', 'CustomerPayBillOnline'),
            'mpesa_callback_url' => Setting::get('mpesa_callback_url', url('/api/v1/mpesa/callback')),

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
            'mail_template_report_ack' => Setting::get('mail_template_report_ack', "Dear {REPORTER_NAME},\n\nThank you for submitting a report regarding the obituary for {DECEASED_NAME}.\n\nOur editorial and moderation team has received your report regarding '{REASON}' and is actively investigating the matter. We will notify you once a determination is made.\n\nWarm regards,\nObituaries.co.ke Moderation Team"),
            'mail_template_report_resolved' => Setting::get('mail_template_report_resolved', "Dear {REPORTER_NAME},\n\nYour report concerning the obituary for {DECEASED_NAME} has been reviewed and updated by our moderation team.\n\nStatus: {STATUS}\nResolution Notes: {NOTES}\n\nThank you for helping us maintain accuracy and dignity on Obituaries.co.ke.\n\nWarm regards,\nObituaries.co.ke Moderation Team"),

            // SMS Gateway & Templates
            'sms_provider' => Setting::get('sms_provider', 'textsms'),
            'sms_api_key' => Setting::get('sms_api_key', ''),
            'sms_partner_id' => Setting::get('sms_partner_id', ''),
            'sms_shortcode' => Setting::get('sms_shortcode', 'OBITUARIES'),
            'sms_sender_id' => Setting::get('sms_sender_id', 'OBITUARIES'),
            'sms_template_submission' => Setting::get('sms_template_submission', "Dear {NAME}, your obituary submission for {DECEASED_NAME} has been received. Complete payment to publish."),
            'sms_template_approval' => Setting::get('sms_template_approval', "Dear {NAME}, the obituary for {DECEASED_NAME} is now published live: {LINK}"),
            'sms_template_rejection' => Setting::get('sms_template_rejection', "Dear {NAME}, your obituary submission for {DECEASED_NAME} was not approved. Reason: {REASON}"),
            'sms_template_anniversary' => Setting::get('sms_template_anniversary', "Dear {NAME}, today marks the {YEARS} Anniversary of {DECEASED_NAME}'s passing. We join you in memory: {LINK}"),
            'sms_template_report_ack' => Setting::get('sms_template_report_ack', "Dear {REPORTER_NAME}, your report regarding {DECEASED_NAME}'s obituary notice has been received. Ref #{REPORT_ID}."),
            'sms_template_report_resolved' => Setting::get('sms_template_report_resolved', "Dear {REPORTER_NAME}, your report regarding {DECEASED_NAME}'s obituary has been updated to: {STATUS}. Notes: {NOTES}"),
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
            'auto_publish_obituaries' => ['nullable', 'string', 'in:0,1'],
            'show_poster_details' => ['nullable', 'string', 'in:0,1'],
            'mpesa_env' => ['nullable', 'string', 'in:sandbox,live'],
            'mpesa_shortcode' => ['nullable', 'string', 'max:50'],
            'mpesa_passkey' => ['nullable', 'string'],
            'mpesa_consumer_key' => ['nullable', 'string'],
            'mpesa_consumer_secret' => ['nullable', 'string'],
            'mpesa_mock_mode' => ['nullable', 'string', 'in:0,1'],
            'mpesa_transaction_type' => ['nullable', 'string', 'in:CustomerPayBillOnline,CustomerBuyGoodsOnline'],
            'mpesa_callback_url' => ['nullable', 'url'],

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
            'mail_template_report_ack' => ['nullable', 'string'],
            'mail_template_report_resolved' => ['nullable', 'string'],

            'sms_provider' => ['nullable', 'string'],
            'sms_api_key' => ['nullable', 'string'],
            'sms_partner_id' => ['nullable', 'string'],
            'sms_shortcode' => ['nullable', 'string'],
            'sms_sender_id' => ['nullable', 'string'],
            'sms_template_submission' => ['nullable', 'string'],
            'sms_template_approval' => ['nullable', 'string'],
            'sms_template_rejection' => ['nullable', 'string'],
            'sms_template_anniversary' => ['nullable', 'string'],
            'sms_template_report_ack' => ['nullable', 'string'],
            'sms_template_report_resolved' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = \App\Helpers\StorageHelper::savePublicFile($request->file('logo'), 'branding');
            Setting::set('logo', $logoPath);
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = \App\Helpers\StorageHelper::savePublicFile($request->file('favicon'), 'branding');
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

        try {
            \App\Services\MailService::sendHtmlEmail(
                $recipient,
                'Obituaries.co.ke SMTP Test Email',
                "Hello!\n\nThis is a test email sent from your Obituaries.co.ke Admin Panel.\nYour SMTP Mail Server configuration and branded HTML email layout are working properly!",
                config('app.url'),
                'Visit Obituaries.co.ke'
            );

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
        $provider = Setting::get('sms_provider', 'textsms');

        $sent = \App\Services\SmsService::send($phone, "Hello! This is a test SMS sent from your Obituaries.co.ke Admin Panel.");

        if ($sent) {
            return back()
                ->with('active_tab', 'sms')
                ->with('success', "📱 Test SMS dispatched successfully to {$phone} via " . strtoupper($provider) . "!");
        } else {
            return back()
                ->with('active_tab', 'sms')
                ->with('error', "Failed to send test SMS via " . strtoupper($provider) . ". Please check API credentials / balance.");
        }
    }

    public function runMigrations(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();

            return back()
                ->with('active_tab', $request->input('active_tab', 'branding'))
                ->with('success', "⚡ Database Migrations Executed Successfully! " . ($output ?: 'Database schema is up to date.'));
        } catch (\Throwable $e) {
            return back()
                ->with('active_tab', $request->input('active_tab', 'branding'))
                ->with('error', "❌ Migration Error: " . $e->getMessage());
        }
    }

    public function runSeeders(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();

            return back()
                ->with('active_tab', $request->input('active_tab', 'branding'))
                ->with('success', "🌱 Database Seeders Executed Successfully! " . ($output ?: 'Database seeded.'));
        } catch (\Throwable $e) {
            return back()
                ->with('active_tab', $request->input('active_tab', 'branding'))
                ->with('error', "❌ Seeder Error: " . $e->getMessage());
        }
    }

    public function gitPull(Request $request)
    {
        try {
            $basePath = base_path();
            $command = "cd " . escapeshellarg($basePath) . " && git config --global --add safe.directory " . escapeshellarg($basePath) . " 2>&1 && git pull origin main 2>&1";
            $output = shell_exec($command);

            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            return back()
                ->with('active_tab', $request->input('active_tab', 'database'))
                ->with('success', "🔄 Git Pull Executed Successfully! Output: " . trim($output ?: 'Already up to date.'));
        } catch (\Throwable $e) {
            return back()
                ->with('active_tab', $request->input('active_tab', 'database'))
                ->with('error', "❌ Git Pull Error: " . $e->getMessage());
        }
    }

    public function fixStorage(Request $request)
    {
        try {
            $publicStoragePath = public_path('storage');
            $targetStoragePath = storage_path('app/public');

            if (!file_exists($targetStoragePath)) {
                mkdir($targetStoragePath, 0755, true);
            }
            if (!file_exists($targetStoragePath . '/obituaries')) {
                mkdir($targetStoragePath . '/obituaries', 0755, true);
            }

            if (function_exists('symlink')) {
                if (is_link($publicStoragePath)) {
                    @unlink($publicStoragePath);
                }
                \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
                $output = \Illuminate\Support\Facades\Artisan::output();
            } else {
                if (is_link($publicStoragePath)) {
                    @unlink($publicStoragePath);
                }
                if (!file_exists($publicStoragePath)) {
                    mkdir($publicStoragePath, 0755, true);
                    mkdir($publicStoragePath . '/obituaries', 0755, true);
                }

                // Copy files recursively
                $copyDir = function ($src, $dst) use (&$copyDir) {
                    $dir = @opendir($src);
                    if (!$dir) return;
                    @mkdir($dst, 0755, true);
                    while (false !== ($file = readdir($dir))) {
                        if (($file != '.') && ($file != '..')) {
                            if (is_dir($src . '/' . $file)) {
                                $copyDir($src . '/' . $file, $dst . '/' . $file);
                            } else {
                                @copy($src . '/' . $file, $dst . '/' . $file);
                            }
                        }
                    }
                    closedir($dir);
                };
                $copyDir($targetStoragePath, $publicStoragePath);
                $output = "Copied files directly to public/storage (symlink() is disabled on server).";
            }

            return back()
                ->with('active_tab', $request->input('active_tab', 'database'))
                ->with('success', "🖼️ Storage Repaired Successfully! " . trim($output));
        } catch (\Throwable $e) {
            return back()
                ->with('active_tab', $request->input('active_tab', 'database'))
                ->with('error', "❌ Storage Repair Error: " . $e->getMessage());
        }
    }

    public function purgeDatabase(Request $request)
    {
        $validated = $request->validate([
            'target' => ['required', 'string', 'in:all,obituaries,payments,reports,candles'],
            'confirm_text' => ['required', 'string'],
        ]);

        if (strtoupper(trim($validated['confirm_text'])) !== 'PURGE') {
            return back()
                ->with('active_tab', 'database')
                ->with('error', '❌ Confirmation text did not match. Please type PURGE in capital letters to confirm database cleanup.');
        }

        $driver = DB::connection()->getDriverName();

        try {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            $target = $validated['target'];
            $messages = [];

            if ($target === 'all' || $target === 'obituaries') {
                // Delete all obituaries and related records
                DB::table('candles')->truncate();
                DB::table('obituary_reports')->truncate();
                DB::table('payments')->truncate();
                DB::table('obituaries')->truncate();

                // Clean up media storage files in storage/app/public/obituaries
                $storageDir = storage_path('app/public/obituaries');
                if (file_exists($storageDir)) {
                    $files = glob($storageDir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }
                }

                $messages[] = "Obituaries, Candles, Reports, Payments & Storage Uploads";
            } elseif ($target === 'payments') {
                DB::table('payments')->truncate();
                $messages[] = "Payment Transactions & Audit Logs";
            } elseif ($target === 'reports') {
                DB::table('obituary_reports')->truncate();
                $messages[] = "Obituary Flag Reports";
            } elseif ($target === 'candles') {
                DB::table('candles')->truncate();
                $messages[] = "Tribute Candles & Condolences";
            }

            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            $summary = implode(', ', $messages);
            return back()
                ->with('active_tab', 'database')
                ->with('success', "🧹 Database Cleanup Successful! Wiped: {$summary}. The platform is clean and ready for live production.");

        } catch (\Throwable $e) {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            return back()
                ->with('active_tab', 'database')
                ->with('error', "❌ Database Cleanup Error: " . $e->getMessage());
        }
    }
}
