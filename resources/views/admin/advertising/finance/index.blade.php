@extends('layouts.admin')

@section('title', 'Advertising Financial Revenue Reports')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Advertising Financial Revenue</h1>
            <p class="text-xs text-slate-400">Track M-Pesa campaign sales, daily/weekly/monthly revenue breakdowns, and placement performance.</p>
        </div>
        <a href="{{ route('admin.advertising.finance.export') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center space-x-1.5 shadow-md">
            <span class="material-symbols-outlined text-[18px]">download</span>
            <span>Export Revenue Statement (CSV)</span>
        </a>
    </div>

    <!-- Revenue Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Today's Revenue</span>
            <div class="text-xl font-extrabold text-emerald-400 font-mono">KES {{ number_format($todayRevenue) }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Weekly Revenue</span>
            <div class="text-xl font-extrabold text-emerald-400 font-mono">KES {{ number_format($weeklyRevenue) }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Monthly Revenue</span>
            <div class="text-xl font-extrabold text-emerald-400 font-mono">KES {{ number_format($monthlyRevenue) }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Annual Revenue</span>
            <div class="text-xl font-extrabold text-amber-400 font-mono">KES {{ number_format($annualRevenue) }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Outstanding</span>
            <div class="text-xl font-extrabold text-rose-400 font-mono">KES {{ number_format($outstandingPayments) }}</div>
        </div>
    </div>

    <!-- Breakdown Grids -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Revenue by Placement Slot</h3>
            <div class="space-y-3">
                @foreach($revenueByPlacement as $rp)
                    <div class="flex items-center justify-between text-xs border-b border-slate-800 pb-2">
                        <span class="text-slate-300 font-semibold">{{ $rp->name }}</span>
                        <span class="font-mono text-emerald-400 font-bold">KES {{ number_format($rp->total) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Revenue by Business Category</h3>
            <div class="space-y-3">
                @foreach($revenueByCategory as $rc)
                    <div class="flex items-center justify-between text-xs border-b border-slate-800 pb-2">
                        <span class="text-slate-300 font-semibold">{{ $rc->name ?: 'General Funeral Services' }}</span>
                        <span class="font-mono text-emerald-400 font-bold">KES {{ number_format($rc->total) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Payments List -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Recent M-Pesa Campaign Transactions</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Receipt / Ref</th>
                        <th class="py-3 px-4">Advertiser Business</th>
                        <th class="py-3 px-4">M-Pesa Phone</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Paid Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($recentPayments as $pay)
                        <tr class="hover:bg-slate-800/50">
                            <td class="py-3 px-4 font-mono font-bold text-white">
                                {{ $pay->mpesa_receipt_number ?: $pay->checkout_request_id }}
                            </td>
                            <td class="py-3 px-4 text-slate-200 font-semibold">
                                {{ $pay->campaign->advertiser->business_name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $pay->phone_number }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-400">
                                KES {{ number_format($pay->amount) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $pay->status === 'completed' ? 'bg-emerald-950 text-emerald-300 border-emerald-800' : 'bg-rose-950 text-rose-300 border-rose-800' }}">
                                    {{ $pay->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-400 font-mono">
                                {{ $pay->paid_at ? $pay->paid_at->format('M d, Y H:i') : $pay->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
