<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $period = $request->input('period', 'all');

        $query = Payment::with('obituary');

        // Apply Preset Date Periods if selected
        if ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($period === 'this_year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        // Custom Date Range Filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Status Filter
        if ($status) {
            $query->where('status', $status);
        }

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mpesa_receipt_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('checkout_request_id', 'like', "%{$search}%")
                  ->orWhereHas('obituary', function ($oq) use ($search) {
                      $oq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Financial Metrics Summary (All-time & Period Metrics)
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $thisMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $completedCount = Payment::where('status', 'completed')->count();
        $failedCount = Payment::where('status', 'failed')->count();
        $pendingCount = Payment::where('status', 'pending')->count();
        $totalCount = Payment::count();
        $successRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;

        // Daily Revenue Trends (Last 14 Days)
        $dailyTrends = Payment::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $maxDailyRevenue = $dailyTrends->max('total_amount') ?: 1;

        $payments = (clone $query)->latest('id')->paginate(20)->withQueryString();

        return view('admin.payments.index', compact(
            'payments',
            'status',
            'search',
            'dateFrom',
            'dateTo',
            'period',
            'totalRevenue',
            'thisMonthRevenue',
            'todayRevenue',
            'completedCount',
            'failedCount',
            'pendingCount',
            'totalCount',
            'successRate',
            'dailyTrends',
            'maxDailyRevenue'
        ));
    }

    public function export(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Payment::with('obituary');

        if ($status) {
            $query->where('status', $status);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mpesa_receipt_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest('id')->get();

        $fileName = 'finance_report_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'M-Pesa Receipt', 'Phone Number', 'Obituary Name', 'Amount (KES)', 'Status', 'Checkout Request ID', 'Result Code', 'Result Description', 'Date']);

            foreach ($payments as $pay) {
                fputcsv($file, [
                    $pay->id,
                    $pay->mpesa_receipt_number ?? 'N/A',
                    $pay->phone_number,
                    $pay->obituary ? $pay->obituary->full_name : 'N/A',
                    $pay->amount,
                    strtoupper($pay->status),
                    $pay->checkout_request_id,
                    $pay->result_code ?? '',
                    $pay->result_desc ?? '',
                    $pay->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
