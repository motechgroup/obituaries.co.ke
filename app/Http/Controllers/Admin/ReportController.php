<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObituaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = ObituaryReport::with(['obituary', 'resolver']);

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function resolve(Request $request, ObituaryReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:reviewed,resolved,dismissed'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'resolution_notes' => $validated['resolution_notes'] ?? null,
            'resolved_by' => Auth::guard('admin')->id(),
        ]);

        $report->load('obituary');

        // Notify reporter via Email if email is available
        if (!empty($report->reporter_email)) {
            try {
                \App\Services\MailService::configure();
                $tmpl = \App\Models\Setting::get('mail_template_report_resolved', "Dear {REPORTER_NAME},\n\nYour report concerning the obituary for {DECEASED_NAME} has been reviewed and updated by our moderation team.\n\nStatus: {STATUS}\nResolution Notes: {NOTES}\n\nThank you for helping us maintain accuracy and dignity on Obituaries.co.ke.\n\nWarm regards,\nObituaries.co.ke Moderation Team");

                $body = str_replace(
                    ['{REPORTER_NAME}', '{DECEASED_NAME}', '{STATUS}', '{NOTES}'],
                    [
                        $report->reporter_name,
                        $report->obituary->full_name ?? 'Obituary Notice',
                        strtoupper($validated['status']),
                        $validated['resolution_notes'] ?: 'Reviewed by moderation team.'
                    ],
                    $tmpl
                );

                \Illuminate\Support\Facades\Mail::raw($body, function ($msg) use ($report) {
                    $msg->to($report->reporter_email)
                        ->subject("Report Status Update: Obituary Notice #{$report->obituary_id}");
                });
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
