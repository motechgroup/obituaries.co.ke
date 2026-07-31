<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObituaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\BlockedIp;
use App\Models\SecurityLog;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Auto-heal missing columns on live server if migration hasn't been manually executed yet
        if (Schema::hasTable('obituary_reports') && !Schema::hasColumn('obituary_reports', 'is_system_flagged')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $hasFlagColumn = Schema::hasTable('obituary_reports') && Schema::hasColumn('obituary_reports', 'is_system_flagged');

        $status = $request->input('status');
        $query = ObituaryReport::with(['obituary', 'resolver']);

        if ($status === 'system_flagged') {
            if ($hasFlagColumn) {
                $query->where('is_system_flagged', true);
            } else {
                $query->where('status', 'flagged_spam');
            }
        } elseif ($status === 'pending') {
            if ($hasFlagColumn) {
                $query->where('status', 'pending')->where('is_system_flagged', false);
            } else {
                $query->where('status', 'pending');
            }
        } elseif ($status === 'spam') {
            $query->whereIn('status', ['spam', 'flagged_spam']);
        } elseif ($status) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();
        $systemFlaggedCount = $hasFlagColumn 
            ? ObituaryReport::where('is_system_flagged', true)->where('status', 'flagged_spam')->count() 
            : 0;

        return view('admin.reports.index', compact('reports', 'status', 'systemFlaggedCount'));
    }

    public function resolve(Request $request, ObituaryReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:reviewed,resolved,dismissed,spam'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = $validated['status'];

        $report->update([
            'status' => $status,
            'resolution_notes' => $validated['resolution_notes'] ?? null,
            'resolved_by' => Auth::guard('admin')->id(),
        ]);

        // If marked as spam, automatically block the offender's IP address
        if ($status === 'spam') {
            if (!empty($report->ip_address)) {
                BlockedIp::firstOrCreate(
                    ['ip_address' => $report->ip_address],
                    [
                        'reason' => "Automated block: Marked as spam offender on Report #{$report->id}",
                        'blocked_by' => Auth::guard('admin')->id(),
                    ]
                );
            }
            SecurityLog::log('report_marked_spam', 'warning', $report->obituary_id, "Report #{$report->id} marked as spam by Admin. IP {$report->ip_address} blocked.");

            return back()->with('success', "Report #{$report->id} marked as SPAM. IP address {$report->ip_address} has been automatically blocked.");
        }

        $report->load('obituary');

        // Notify reporter via Email if email is available
        if (!empty($report->reporter_email)) {
            try {
                $obituaryLink = $report->obituary ? route('obituaries.show', $report->obituary->slug) : config('app.url');
                $tmpl = \App\Models\Setting::get('mail_template_report_resolved', "Dear {REPORTER_NAME},\n\nYour report concerning the obituary notice for {DECEASED_NAME} has been reviewed and updated by our moderation team.\n\nReported Obituary Link: {LINK}\nStatus: {STATUS}\nResolution Notes: {NOTES}\n\nThank you for helping us maintain accuracy and dignity on Obituaries.co.ke.\n\nWarm regards,\nObituaries.co.ke Moderation Team");

                $body = str_replace(
                    ['{REPORTER_NAME}', '{DECEASED_NAME}', '{STATUS}', '{NOTES}', '{LINK}'],
                    [
                        $report->reporter_name,
                        $report->obituary->full_name ?? 'Obituary Notice',
                        strtoupper($validated['status']),
                        $validated['resolution_notes'] ?: 'Reviewed by moderation team.',
                        $obituaryLink
                    ],
                    $tmpl
                );

                \App\Services\MailService::sendHtmlEmail(
                    $report->reporter_email,
                    "Report Status Update: Obituary Notice #{$report->obituary_id}",
                    $body,
                    $obituaryLink,
                    'View Reported Obituary Notice'
                );
            } catch (\Throwable $e) {}
        }

        // Notify reporter via SMS if phone is available
        if (!empty($report->reporter_phone)) {
            try {
                $smsTmpl = \App\Models\Setting::get('sms_template_report_resolved', "Dear {REPORTER_NAME}, your report regarding {DECEASED_NAME}'s obituary has been updated to: {STATUS}. Notes: {NOTES}");
                $smsMessage = str_replace(
                    ['{REPORTER_NAME}', '{DECEASED_NAME}', '{STATUS}', '{NOTES}'],
                    [
                        $report->reporter_name,
                        $report->obituary->full_name ?? 'Obituary',
                        strtoupper($validated['status']),
                        $validated['resolution_notes'] ?: 'Reviewed by team.'
                    ],
                    $smsTmpl
                );
                \App\Services\SmsService::send($report->reporter_phone, $smsMessage);
            } catch (\Throwable $e) {}
        }

        return back()->with('success', "Report #{$report->id} has been marked as '{$validated['status']}' and notification dispatched.");
    }

    public function destroy(ObituaryReport $report)
    {
        $report->delete();
        return back()->with('success', 'Report deleted successfully.');
    }
}
