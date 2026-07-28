<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\ObituaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class ReportController extends Controller
{
    public function store(Request $request, Obituary $obituary)
    {
        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_email' => ['required', 'email', 'max:255'],
            'reporter_phone' => ['required', 'string', 'max:50'],
            'reason' => ['required', 'string', 'in:inaccurate_info,impersonation,unauthorized_post,copyright_violation,offensive_content,other'],
            'details' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        // Auto-heal: Ensure obituary_reports table exists in database
        if (!Schema::hasTable('obituary_reports')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $report = ObituaryReport::create([
            'obituary_id' => $obituary->id,
            'reporter_name' => strip_tags($validated['reporter_name']),
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => strip_tags($validated['reporter_phone']),
            'reason' => $validated['reason'],
            'details' => strip_tags($validated['details']),
            'status' => 'pending',
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
