<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obituary;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $isSuperAdmin = $admin ? $admin->isSuperAdmin() : false;

        $totalObituaries = Obituary::count();
        $pendingPaymentCount = Obituary::where('status', 'pending_payment')->count();
        $pendingVerificationCount = Obituary::where('status', 'pending_verification')->count();
        $publishedCount = Obituary::where('status', 'published')->count();
        
        // Financial analytics (Only for Super Admin)
        $totalRevenue = $isSuperAdmin ? Payment::where('status', 'completed')->sum('amount') : 0;

        // Content moderation metrics
        $pendingReportsCount = \App\Models\ObituaryReport::where('status', 'pending')->count();
        $myVerifiedCount = $admin ? Obituary::where('verified_by', $admin->id)->count() : 0;

        // Recent submissions regardless of status
        $recentSubmissions = Obituary::latest('id')
            ->take(10)
            ->get();

        // Queue specifically awaiting verification
        $pendingObituaries = Obituary::where('status', 'pending_verification')
            ->latest('id')
            ->take(8)
            ->get();

        // Pending user reports requiring editorial review
        $pendingReports = \App\Models\ObituaryReport::where('status', 'pending')
            ->with('obituary')
            ->latest('id')
            ->take(5)
            ->get();

        $recentPayments = $isSuperAdmin ? Payment::where('status', 'completed')
            ->with('obituary')
            ->latest('id')
            ->take(5)
            ->get() : collect();

        return view('admin.dashboard', compact(
            'totalObituaries',
            'pendingPaymentCount',
            'pendingVerificationCount',
            'publishedCount',
            'totalRevenue',
            'pendingReportsCount',
            'myVerifiedCount',
            'recentSubmissions',
            'pendingObituaries',
            'pendingReports',
            'recentPayments',
            'isSuperAdmin'
        ));
    }
}
