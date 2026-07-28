<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Obituary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', '30_days');

        $query = PageView::query();

        // Apply Timeframe Filter
        if ($range === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === '7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === '30_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($range === 'year') {
            $query->where('created_at', '>=', Carbon::now()->subYear());
        }

        $totalPageViews = (clone $query)->count();
        $uniqueVisitors = (clone $query)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address');

        // Device Breakdown
        $deviceBreakdown = (clone $query)
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        // Traffic Sources / Referrers Breakdown
        $referrers = (clone $query)
            ->select('referer_host', DB::raw('count(*) as count'))
            ->groupBy('referer_host')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        // Top Most Viewed Obituaries
        $topObituaries = (clone $query)
            ->whereNotNull('obituary_id')
            ->select('obituary_id', DB::raw('count(*) as views_count'))
            ->groupBy('obituary_id')
            ->orderByDesc('views_count')
            ->with('obituary')
            ->take(10)
            ->get();

        // Top Visited Routes / Pages
        $topPages = (clone $query)
            ->select('url', DB::raw('count(*) as count'))
            ->groupBy('url')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Daily Traffic Trend (Last 14 Days)
        $dailyTrends = PageView::where('created_at', '>=', Carbon::now()->subDays(14))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_views'),
                DB::raw('count(DISTINCT ip_address) as unique_visitors')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return view('admin.analytics.index', compact(
            'totalPageViews',
            'uniqueVisitors',
            'deviceBreakdown',
            'referrers',
            'topObituaries',
            'topPages',
            'dailyTrends',
            'range'
        ));
    }
}
