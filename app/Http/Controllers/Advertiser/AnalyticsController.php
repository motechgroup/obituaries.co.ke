<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdClick;
use App\Models\AdImpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $advertiser = Auth::guard('advertiser')->user();
        $campaigns = AdCampaign::where('advertiser_id', $advertiser->id)->get();
        $campaignIds = $campaigns->pluck('id')->toArray();

        $selectedCampaignId = $request->input('campaign_id');
        if ($selectedCampaignId && in_array($selectedCampaignId, $campaignIds)) {
            $filteredCampaignIds = [$selectedCampaignId];
        } else {
            $filteredCampaignIds = $campaignIds;
        }

        $impressionsCount = AdImpression::whereIn('ad_campaign_id', $filteredCampaignIds)->count();
        $clicksCount = AdClick::whereIn('ad_campaign_id', $filteredCampaignIds)->count();
        $ctr = $impressionsCount > 0 ? round(($clicksCount / $impressionsCount) * 100, 2) : 0.00;

        // Daily impressions & clicks for chart (past 30 days)
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $imp = AdImpression::whereIn('ad_campaign_id', $filteredCampaignIds)
                ->whereDate('created_at', $date)
                ->count();
            $clk = AdClick::whereIn('ad_campaign_id', $filteredCampaignIds)
                ->whereDate('created_at', $date)
                ->count();
            $dailyData[] = [
                'date' => now()->subDays($i)->format('M d'),
                'impressions' => $imp,
                'clicks' => $clk,
            ];
        }

        // Top Performing Counties
        $topCounties = AdImpression::select('county', DB::raw('count(*) as total'))
            ->whereIn('ad_campaign_id', $filteredCampaignIds)
            ->whereNotNull('county')
            ->groupBy('county')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Device Breakdowns
        $devices = AdImpression::select('device_type', DB::raw('count(*) as total'))
            ->whereIn('ad_campaign_id', $filteredCampaignIds)
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        return view('advertiser.analytics.index', compact(
            'advertiser',
            'campaigns',
            'selectedCampaignId',
            'impressionsCount',
            'clicksCount',
            'ctr',
            'dailyData',
            'topCounties',
            'devices'
        ));
    }

    public function exportCsv(Request $request)
    {
        $advertiser = Auth::guard('advertiser')->user();
        $campaigns = AdCampaign::with(['placement', 'bannerSize'])
            ->where('advertiser_id', $advertiser->id)
            ->withCount(['impressions', 'clicks'])
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=advertiser-analytics-" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($campaigns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Campaign Name', 'Placement', 'Dimensions', 'Status', 'Start Date', 'End Date', 'Impressions', 'Clicks', 'CTR %', 'Amount (KES)']);

            foreach ($campaigns as $c) {
                fputcsv($file, [
                    $c->name,
                    $c->placement->name ?? 'N/A',
                    $c->bannerSize->dimensions ?? 'N/A',
                    strtoupper($c->status),
                    $c->start_date->format('Y-m-d'),
                    $c->end_date->format('Y-m-d'),
                    $c->impressions_count,
                    $c->clicks_count,
                    $c->ctr . '%',
                    $c->calculated_price,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
