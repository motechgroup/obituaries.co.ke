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
        $totalObituaries = Obituary::count();
        $pendingPaymentCount = Obituary::where('status', 'pending_payment')->count();
        $pendingVerificationCount = Obituary::where('status', 'pending_verification')->count();
        $publishedCount = Obituary::where('status', 'published')->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        // Recent submissions regardless of status
        $recentSubmissions = Obituary::latest('id')
            ->take(10)
            ->get();

        // Queue specifically awaiting verification
        $pendingObituaries = Obituary::where('status', 'pending_verification')
            ->latest('id')
            ->take(5)
            ->get();

        $recentPayments = Payment::where('status', 'completed')
            ->with('obituary')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalObituaries',
            'pendingPaymentCount',
            'pendingVerificationCount',
            'publishedCount',
            'totalRevenue',
            'recentSubmissions',
            'pendingObituaries',
            'recentPayments'
        ));
    }
}
