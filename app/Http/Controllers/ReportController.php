<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\ObituaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

use App\Services\SpamProtectionService;
use App\Models\BlockedIp;
use App\Models\SecurityLog;

class ReportController extends Controller
{
    public function store(Request $request, Obituary $obituary)
    {
        // 1. Check if IP address is blocked
        if (BlockedIp::isBlocked($request->ip())) {
            return back()->withErrors(['security' => 'Please review your submission details and try again.'])->withInput();
        }

        // 2. Honeypot Check (Must be empty)
        if (!SpamProtectionService::checkHoneypot($request->input('website_hp'))) {
            SecurityLog::log('report_honeypot_triggered', 'warning', $obituary->id, "Honeypot filled by IP {$request->ip()}");
            return back()->with('success', '🚩 Your report has been submitted to our moderation team. We will review it promptly.');
        }

        // 3. Time-lock Submission Check (Minimum 3 Seconds)
        if (!SpamProtectionService::verifyTimeLock($request->input('_form_time'), 3)) {
            SecurityLog::log('report_fast_submit', 'warning', $obituary->id, "Fast form submission (<3s) from IP {$request->ip()}");
            return back()->withErrors(['details' => 'Please review your submission details and try again.'])->withInput();
        }

        // 4. Cloudflare Turnstile Verification
        if (!SpamProtectionService::verifyTurnstile($request->input('cf-turnstile-response'), $request->ip())) {
            SecurityLog::log('report_turnstile_failed', 'warning', $obituary->id, "Turnstile verification failed for IP {$request->ip()}");
            return back()->withErrors(['security' => 'Security verification check failed. Please try again.'])->withInput();
        }

        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_email' => ['required', 'email', 'max:255'],
            'reporter_phone' => ['required', 'string', 'max:50'],
            'reason' => ['required', 'string', 'in:inaccurate_info,impersonation,unauthorized_post,copyright_violation,offensive_content,other'],
            'details' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        // 5. Disposable Email Provider Check
        if (SpamProtectionService::isDisposableEmail($validated['reporter_email'])) {
            SecurityLog::log('report_disposable_email', 'warning', $obituary->id, "Disposable email blocked: {$validated['reporter_email']} from IP {$request->ip()}");
            return back()->withErrors(['reporter_email' => 'Please provide a valid personal or business email address.'])->withInput();
        }

        // 6. Kenyan Phone Number Check
        if (!SpamProtectionService::isKenyanPhone($validated['reporter_phone'])) {
            SecurityLog::log('report_invalid_phone', 'warning', $obituary->id, "Non-Kenyan phone blocked: {$validated['reporter_phone']} from IP {$request->ip()}");
            return back()->withErrors(['reporter_phone' => 'Please enter a valid Kenyan phone number (e.g. 0712345678 or +254712345678).'])->withInput();
        }

        // 7. Gibberish & Random-Character Content Detection
        if (SpamProtectionService::isGibberish($validated['reporter_name']) || SpamProtectionService::isGibberish($validated['details'])) {
            SecurityLog::log('report_gibberish_detected', 'warning', $obituary->id, "Gibberish spam content blocked from IP {$request->ip()}");
            return back()->withErrors(['details' => 'Please provide clear and readable details for your report.'])->withInput();
        }

        // Auto-heal: Ensure obituary_reports table exists in database
        if (!Schema::hasTable('obituary_reports')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        // 8. Log IP Address, User Agent, and Submission Timestamp
        $report = ObituaryReport::create([
            'obituary_id' => $obituary->id,
            'reporter_name' => strip_tags($validated['reporter_name']),
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => strip_tags($validated['reporter_phone']),
            'reason' => $validated['reason'],
            'details' => strip_tags($validated['details']),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
        ]);

        \App\Models\SecurityLog::log('report_submitted', 'warning', $obituary->id, "Notice #{$obituary->id} reported by {$report->reporter_name} ({$report->reason})");

        // Dispatch Confirmation Email to Reporter
        if (!empty($validated['reporter_email'])) {
            try {
                $obituaryLink = route('obituaries.show', $obituary->slug);
                $tmpl = \App\Models\Setting::get('mail_template_report_ack', "Dear {REPORTER_NAME},\n\nThank you for submitting a report regarding the obituary notice for {DECEASED_NAME}.\n\nReported Obituary Link: {LINK}\n\nOur editorial and moderation team has received your report regarding '{REASON}' and is actively investigating the matter. We will notify you once a determination is made.\n\nWarm regards,\nObituaries.co.ke Moderation Team");
                $body = str_replace(
                    ['{REPORTER_NAME}', '{DECEASED_NAME}', '{REASON}', '{LINK}'],
                    [$validated['reporter_name'], $obituary->full_name, ucfirst(str_replace('_', ' ', $validated['reason'])), $obituaryLink],
                    $tmpl
                );
                \App\Services\MailService::sendHtmlEmail(
                    $validated['reporter_email'],
                    "Report Received: Obituary Notice for {$obituary->full_name}",
                    $body,
                    $obituaryLink,
                    'View Reported Obituary Notice'
                );
            } catch (\Throwable $e) {}
        }

        // Dispatch Confirmation SMS to Reporter if phone provided
        if (!empty($validated['reporter_phone'])) {
            try {
                $smsTmpl = \App\Models\Setting::get('sms_template_report_ack', "Dear {REPORTER_NAME}, your report regarding {DECEASED_NAME}'s obituary notice has been received. Ref #{REPORT_ID}.");
                $smsMessage = str_replace(
                    ['{REPORTER_NAME}', '{DECEASED_NAME}', '{REPORT_ID}'],
                    [$validated['reporter_name'], $obituary->full_name, $report->id],
                    $smsTmpl
                );
                \App\Services\SmsService::send($validated['reporter_phone'], $smsMessage);
            } catch (\Throwable $e) {}
        }

        return back()->with('success', '🚩 Your report has been submitted to our moderation team. We will review it promptly.');
    }
}
