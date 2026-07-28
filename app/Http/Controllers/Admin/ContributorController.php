<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContributorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string)$request->input('search'));

        // Query submitters grouped by phone/email/name
        $query = Obituary::select(
            'submitter_name',
            'submitter_phone',
            'submitter_email',
            DB::raw('COUNT(id) as total_notices'),
            DB::raw('SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published_notices'),
            DB::raw('SUM(CASE WHEN status = "pending_payment" OR status = "pending_verification" THEN 1 ELSE 0 END) as pending_notices'),
            DB::raw('MAX(created_at) as last_submission_at')
        );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('submitter_name', 'like', "%{$search}%")
                  ->orWhere('submitter_phone', 'like', "%{$search}%")
                  ->orWhere('submitter_email', 'like', "%{$search}%");
            });
        }

        $contributors = $query->groupBy('submitter_phone', 'submitter_email', 'submitter_name')
            ->orderBy('last_submission_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Attach linked obituaries for each contributor group
        foreach ($contributors as $contributor) {
            $contributor->linked_obituaries = Obituary::where('submitter_phone', $contributor->submitter_phone)
                ->orWhere(function($q) use ($contributor) {
                    if ($contributor->submitter_email) {
                        $q->where('submitter_email', $contributor->submitter_email);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get(['id', 'full_name', 'slug', 'status', 'verification_status', 'county', 'created_at']);
        }

        $totalContributors = Obituary::distinct('submitter_phone')->count('submitter_phone');
        $totalSubmissions = Obituary::count();

        return view('admin.contributors.index', compact('contributors', 'totalContributors', 'totalSubmissions', 'search'));
    }

    public function export()
    {
        $contributors = Obituary::select(
            'submitter_name',
            'submitter_phone',
            'submitter_email',
            DB::raw('COUNT(id) as total_notices'),
            DB::raw('MAX(created_at) as last_submission_at')
        )
        ->groupBy('submitter_phone', 'submitter_email', 'submitter_name')
        ->orderBy('last_submission_at', 'desc')
        ->get();

        $filename = "contributors_report_" . date('Y_m_d_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($contributors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Submitter Name', 'Phone Number', 'Email Address', 'Total Notices', 'Last Submission Date']);

            foreach ($contributors as $c) {
                fputcsv($file, [
                    $c->submitter_name,
                    $c->submitter_phone,
                    $c->submitter_email ?: 'N/A',
                    $c->total_notices,
                    $c->last_submission_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
