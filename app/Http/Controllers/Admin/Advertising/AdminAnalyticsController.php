<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\AdPlacement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $totalImpressions = AdImpression::count();
        $totalClicks = AdClick::count();

        $ctr = ($totalImpressions > 0)
            ? round(($totalClicks / $totalImpressions) * 100, 2)
            : 0.00;

        $runningCampaigns = AdCampaign::where('status', 'running')->count();

        // Performance by Placement Slot
        $placementsPerformance = AdPlacement::withCount(['impressions', 'clicks'])
            ->get()
            ->map(function ($p) {
                $p->ctr = ($p->impressions_count > 0)
                    ? round(($p->clicks_count / $p->impressions_count) * 100, 2)
                    : 0.00;
                return $p;
            });

        // Top Ad Campaigns Performance
        $topCampaigns = AdCampaign::with(['advertiser', 'placement'])
            ->withCount(['impressions', 'clicks'])
            ->orderByDesc('impressions_count')
            ->take(10)
            ->get();

        // Breakdown by Device Type
        $impressionsByDevice = AdImpression::select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->toArray();

        // Breakdown by County
        $impressionsByCounty = AdImpression::select('county', DB::raw('count(*) as total'))
            ->groupBy('county')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return view('admin.advertising.analytics.index', compact(
            'totalImpressions',
            'totalClicks',
            'ctr',
            'runningCampaigns',
            'placementsPerformance',
            'topCampaigns',
            'impressionsByDevice',
            'impressionsByCounty'
        ));
    }

    public function exportCsv()
    {
        $impressions = AdImpression::with(['campaign', 'placement'])->latest()->take(5000)->get();

        $filename = "ad-analytics-report-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($impressions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Impression ID', 'Campaign Name', 'Placement', 'County', 'Device', 'Browser', 'OS', 'IP Address', 'Timestamp']);

            foreach ($impressions as $imp) {
                fputcsv($file, [
                    $imp->id,
                    $imp->campaign->name ?? 'N/A',
                    $imp->placement->name ?? 'N/A',
                    $imp->county,
                    $imp->device_type,
                    $imp->browser,
                    $imp->os,
                    $imp->ip_address,
                    $imp->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
