<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdClick;
use App\Models\AdImpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $advertiser = Auth::guard('advertiser')->user();

        $campaigns = AdCampaign::with(['placement', 'bannerSize', 'businessProfile'])
            ->where('advertiser_id', $advertiser->id)
            ->withCount(['impressions', 'clicks'])
            ->latest()
            ->get();

        $totalCampaignsCount = $campaigns->count();
        $runningCampaignsCount = $campaigns->where('status', 'running')->count();
        $expiredCampaignsCount = $campaigns->where('status', 'expired')->count();
        $pendingCampaignsCount = $campaigns->whereIn('status', ['submitted', 'pending_approval', 'payment_pending'])->count();

        $campaignIds = $campaigns->pluck('id')->toArray();

        $totalImpressions = AdImpression::whereIn('ad_campaign_id', $campaignIds)->count();
        $totalClicks = AdClick::whereIn('ad_campaign_id', $campaignIds)->count();
        $averageCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.00;

        $totalSpent = $campaigns->whereIn('status', ['approved', 'running', 'expired', 'payment_completed'])->sum('calculated_price');

        $recentCampaigns = $campaigns->take(5);

        return view('advertiser.dashboard', compact(
            'advertiser',
            'totalCampaignsCount',
            'runningCampaignsCount',
            'expiredCampaignsCount',
            'pendingCampaignsCount',
            'totalImpressions',
            'totalClicks',
            'averageCtr',
            'totalSpent',
            'recentCampaigns'
        ));
    }
}
