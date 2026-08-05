<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdCampaignPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFinanceController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');
        $startOfWeek = now()->startOfWeek()->format('Y-m-d');
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $startOfYear = now()->startOfYear()->format('Y-m-d');

        $completedPayments = AdCampaignPayment::where('status', 'completed');

        $todayRevenue = (clone $completedPayments)->whereDate('paid_at', $today)->sum('amount');
        $weeklyRevenue = (clone $completedPayments)->whereDate('paid_at', '>=', $startOfWeek)->sum('amount');
        $monthlyRevenue = (clone $completedPayments)->whereDate('paid_at', '>=', $startOfMonth)->sum('amount');
        $annualRevenue = (clone $completedPayments)->whereDate('paid_at', '>=', $startOfYear)->sum('amount');

        $outstandingPayments = AdCampaign::where('status', 'payment_pending')->sum('calculated_price');

        // Revenue by Placement
        $revenueByPlacement = DB::table('ad_campaign_payments')
            ->join('ad_campaigns', 'ad_campaign_payments.ad_campaign_id', '=', 'ad_campaigns.id')
            ->join('ad_placements', 'ad_campaigns.ad_placement_id', '=', 'ad_placements.id')
            ->where('ad_campaign_payments.status', 'completed')
            ->select('ad_placements.name', DB::raw('SUM(ad_campaign_payments.amount) as total'))
            ->groupBy('ad_placements.name')
            ->get();

        // Revenue by Category
        $revenueByCategory = DB::table('ad_campaign_payments')
            ->join('ad_campaigns', 'ad_campaign_payments.ad_campaign_id', '=', 'ad_campaigns.id')
            ->leftJoin('business_categories', 'ad_campaigns.business_category_id', '=', 'business_categories.id')
            ->where('ad_campaign_payments.status', 'completed')
            ->select('business_categories.name', DB::raw('SUM(ad_campaign_payments.amount) as total'))
            ->groupBy('business_categories.name')
            ->get();

        $recentPayments = AdCampaignPayment::with('campaign.advertiser')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.advertising.finance.index', compact(
            'todayRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'annualRevenue',
            'outstandingPayments',
            'revenueByPlacement',
            'revenueByCategory',
            'recentPayments'
        ));
    }

    public function exportCsv()
    {
        $payments = AdCampaignPayment::with(['campaign.advertiser', 'campaign.placement'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=ad-revenue-report-" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Receipt No', 'Advertiser', 'Campaign', 'Placement', 'Amount (KES)', 'M-Pesa Phone', 'Paid At']);

            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->mpesa_receipt_number ?: "PAY-{$p->id}",
                    $p->campaign->advertiser->business_name ?? 'N/A',
                    $p->campaign->name ?? 'N/A',
                    $p->campaign->placement->name ?? 'N/A',
                    $p->amount,
                    $p->phone_number,
                    $p->paid_at ? $p->paid_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
