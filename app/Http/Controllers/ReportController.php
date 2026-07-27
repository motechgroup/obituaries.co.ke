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
            'reporter_phone' => ['nullable', 'string', 'max:50'],
            'reason' => ['required', 'string', 'in:inaccurate_info,impersonation,unauthorized_post,copyright_violation,offensive_content,other'],
            'details' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        // Auto-heal: Ensure obituary_reports table exists in database
        if (!Schema::hasTable('obituary_reports')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        ObituaryReport::create([
            'obituary_id' => $obituary->id,
            'reporter_name' => $validated['reporter_name'],
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => $validated['reporter_phone'] ?? null,
            'reason' => $validated['reason'],
            'details' => $validated['details'],
            'status' => 'pending',
        ]);

        return back()->with('success', '🚩 Your report has been submitted to our moderation team. We will review it promptly.');
    }
}
