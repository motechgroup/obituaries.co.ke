@extends('layouts.admin')

@section('title', 'Finance Reports, Analytics & Audit | Admin')

@section('content')
<div class="space-y-6" x-data="{ activePayload: null, payloadModal: false }">
    <!-- Top Header & Export -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Finance Reports, Analytics & Audit Log</h1>
            <p class="text-slate-500 text-sm mt-1">Track revenue performance, payment trends, date filters, and M-Pesa transaction audit logs.</p>
        </div>

        <div>
            <a href="{{ route('admin.payments.export', request()->all()) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center space-x-2">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Download CSV Report</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Revenue -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Revenue (All-Time)</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">
                    KES {{ number_format($totalRevenue, 2) }}
                </div>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 block">Completed M-Pesa Payments</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">payments</span>
            </div>
        </div>

        <!-- This Month Revenue -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">This Month Revenue</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">
                    KES {{ number_format($thisMonthRevenue, 2) }}
                </div>
                <span class="text-[11px] text-amber-700 font-semibold mt-1 block">{{ date('F Y') }} Total</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">calendar_month</span>
            </div>
        </div>

        <!-- Today Revenue -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Revenue</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">
                    KES {{ number_format($todayRevenue, 2) }}
                </div>
                <span class="text-[11px] text-sky-600 font-semibold mt-1 block">{{ date('M d, Y') }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">today</span>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Success Conversion</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">
                    {{ $successRate }}%
                </div>
                <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                    {{ $completedCount }} Paid / {{ $failedCount }} Failed
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">analytics</span>
            </div>
        </div>
    </div>

    <!-- Daily Revenue Trends Visualizer (Last 14 Days) -->
    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-amber-600">trending_up</span>
                    <span>Daily Revenue Trends (Last 14 Days)</span>
                </h3>
                <p class="text-xs text-slate-500">Visualizing completed M-Pesa payment totals per day.</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Completed M-Pesa Transactions</span>
        </div>

        @if(count($dailyTrends) > 0)
            <div class="h-44 flex items-end justify-between gap-2 pt-6 pb-2 px-2 border-b border-slate-200">
                @foreach($dailyTrends as $trend)
                    @php
                        $heightPercent = $maxDailyRevenue > 0 ? min(100, max(12, round(($trend->total_amount / $maxDailyRevenue) * 100))) : 12;
                    @endphp
                    <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                        <!-- Tooltip -->
                        <div class="absolute -top-10 hidden group-hover:flex flex-col items-center z-20 pointer-events-none">
                            <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded shadow-lg whitespace-nowrap">
                                KES {{ number_format($trend->total_amount, 2) }} ({{ $trend->count }} {{ Str::plural('paid', $trend->count) }})
                            </div>
                            <div class="w-2 h-2 bg-slate-900 rotate-45 -mt-1"></div>
                        </div>

                        <!-- Bar -->
                        <div class="w-full max-w-[36px] bg-gradient-to-t from-amber-600 to-amber-400 rounded-t-lg transition-all duration-300 group-hover:from-amber-500 group-hover:to-amber-300" style="height: {{ $heightPercent }}%;"></div>
                        
                        <!-- Date Label -->
                        <span class="text-[10px] text-slate-500 font-semibold mt-2 truncate w-full text-center">
                            {{ \Carbon\Carbon::parse($trend->date)->format('M d') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                No completed M-Pesa transactions recorded in the last 14 days.
            </div>
        @endif
    </div>

    <!-- Filter & Search Controls Card -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs font-semibold">
            <!-- Period Presets -->
            <div>
                <label class="block text-slate-700 uppercase tracking-wider text-[11px] font-bold mb-1">Period Preset</label>
                <select name="period" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-medium">
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-slate-700 uppercase tracking-wider text-[11px] font-bold mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-medium">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-slate-700 uppercase tracking-wider text-[11px] font-bold mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-medium">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-slate-700 uppercase tracking-wider text-[11px] font-bold mb-1">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-medium">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-slate-700 uppercase tracking-wider text-[11px] font-bold mb-1">Search Keywords</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Receipt #, Phone, Name..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-medium">
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="lg:col-span-5 flex items-center justify-end space-x-3 border-t border-slate-100 pt-3">
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                    Reset Filters
                </a>
                <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center space-x-1">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Apply Filters</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-serif text-lg font-bold text-slate-900">Transaction Audit Log</h3>
            <span class="text-xs font-semibold text-slate-500">Showing {{ $payments->count() }} of {{ $payments->total() }} Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">M-Pesa Receipt</th>
                        <th class="px-6 py-3.5">Phone Number</th>
                        <th class="px-6 py-3.5">Obituary Notice</th>
                        <th class="px-6 py-3.5">Amount</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5 text-right">Audit Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($payments as $pay)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">#{{ $pay->id }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">
                                {{ $pay->mpesa_receipt_number ?? 'Pending' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $pay->phone_number }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($pay->obituary)
                                    <a href="{{ route('admin.obituaries.show', $pay->obituary->id) }}" class="text-amber-700 font-semibold hover:underline">
                                        {{ $pay->obituary->full_name }}
                                    </a>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                KES {{ number_format($pay->amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pay->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($pay->status === 'failed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Failed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $pay->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="activePayload = {{ json_encode([
                                    'id' => $pay->id,
                                    'receipt' => $pay->mpesa_receipt_number,
                                    'phone' => $pay->phone_number,
                                    'amount' => $pay->amount,
                                    'status' => $pay->status,
                                    'checkout_id' => $pay->checkout_request_id,
                                    'merchant_id' => $pay->merchant_request_id,
                                    'result_code' => $pay->result_code,
                                    'result_desc' => $pay->result_desc,
                                    'date' => $pay->created_at->format('Y-m-d H:i:s'),
                                    'obituary' => $pay->obituary ? $pay->obituary->full_name : 'N/A',
                                    'raw' => $pay->raw_callback_payload
                                ]) }}; payloadModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-semibold transition-all">
                                    Inspect Payload
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No payment transactions found matching current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Payload Inspection Modal -->
    <div x-show="payloadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200" @click.away="payloadModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <h3 class="font-serif text-xl font-bold text-slate-900">M-Pesa Transaction Audit Details</h3>
                    <p class="text-xs text-slate-500">Transaction ID: #<span x-text="activePayload?.id"></span></p>
                </div>
                <button type="button" @click="payloadModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Receipt Number</span>
                    <span class="font-mono font-bold text-slate-900" x-text="activePayload?.receipt || 'N/A'"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Phone Number</span>
                    <span class="font-medium text-slate-900" x-text="activePayload?.phone"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Checkout Request ID</span>
                    <span class="font-mono text-slate-700 text-[11px] truncate block" x-text="activePayload?.checkout_id"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Merchant Request ID</span>
                    <span class="font-mono text-slate-700 text-[11px] truncate block" x-text="activePayload?.merchant_id"></span>
                </div>
            </div>

            <!-- Result Description -->
            <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">M-Pesa Gate Result</span>
                <p class="text-slate-800 font-medium" x-text="activePayload?.result_desc || 'No response description.'"></p>
            </div>

            <!-- Raw JSON Payload -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Raw Callback JSON Payload</label>
                <pre class="p-4 bg-slate-900 text-emerald-400 rounded-xl text-xs font-mono overflow-x-auto max-h-56 shadow-inner" x-text="JSON.stringify(activePayload?.raw, null, 2) || 'No raw callback payload captured.'"></pre>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" @click="payloadModal = false" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all">
                    Close Audit Window
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
