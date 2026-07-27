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

        return back()->with('success', "Report #{$report->id} has been marked as '{$validated['status']}'.");
    }

    public function destroy(ObituaryReport $report)
    {
        $report->delete();
        return back()->with('success', 'Report deleted successfully.');
    }
}
