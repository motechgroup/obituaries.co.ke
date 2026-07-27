@extends('layouts.admin')

@section('title', 'Admin Dashboard | Obituaries.co.ke')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Administration Overview</h1>
            <p class="text-slate-500 text-sm mt-1">Monitor all submissions, verify submitter contacts, and review M-Pesa payments.</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($pendingPaymentCount > 0)
                <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="inline-flex items-center px-3.5 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-xl text-xs font-bold transition-colors">
                    <span>Pending Payment ({{ $pendingPaymentCount }})</span>
                </a>
            @endif
            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                <span>Pending Verification Queue ({{ $pendingVerificationCount }})</span>
            </a>
        </div>
    </div>

    <!-- Analytics Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Submissions -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Notices</span>
                <span class="font-serif text-2xl font-bold text-slate-900">{{ number_format($totalObituaries) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"/>
                </svg>
            </div>
        </div>

        <!-- Pending Payment -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Pending Payment</span>
                <span class="font-serif text-2xl font-bold text-slate-700">{{ number_format($pendingPaymentCount) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Pending Verification -->
        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider block mb-1">Awaiting Review</span>
                <span class="font-serif text-2xl font-bold text-amber-600">{{ number_format($pendingVerificationCount) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Published Obituaries -->
        <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Published</span>
                <span class="font-serif text-2xl font-bold text-emerald-600">{{ number_format($publishedCount) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Revenue</span>
                <span class="font-serif text-xl font-bold text-slate-900">KES {{ number_format($totalRevenue, 0) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- All Recent Submissions Table (Ensures ALL submitted obituaries appear on dashboard) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-serif text-lg font-bold text-slate-900">All Recent Submissions</h2>
                <p class="text-xs text-slate-500 mt-0.5">Every obituary notice submitted on the platform regardless of payment or verification state.</p>
            </div>
            <a href="{{ route('admin.obituaries.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                Manage Directory &rarr;
            </a>
        </div>

        @if($recentSubmissions->isEmpty())
            <div class="p-8 text-center text-slate-400 text-sm">
                No obituary submissions recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Deceased Name</th>
                            <th class="px-6 py-3.5">Location</th>
                            <th class="px-6 py-3.5">Submitter Details</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Submitted At</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($recentSubmissions as $ob)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <a href="{{ route('admin.obituaries.show', $ob->id) }}" class="hover:text-amber-700">
                                        {{ $ob->full_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    {{ $ob->town }}, {{ $ob->county }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="font-medium text-slate-900">{{ $ob->submitter_name }}</div>
                                    <div class="text-slate-500">{{ $ob->submitter_phone }} ({{ $ob->relationship }})</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($ob->status === 'published')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Published</span>
                                    @elseif($ob->status === 'pending_verification')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Pending Verification</span>
                                    @elseif($ob->status === 'pending_payment')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">Pending Payment</span>
                                    @elseif($ob->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Rejected</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">{{ ucfirst(str_replace('_', ' ', $ob->status)) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $ob->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.obituaries.show', $ob->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs transition-colors">
                                        Inspect / Verify
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Completed Payments -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-serif text-lg font-bold text-slate-900">Recent M-Pesa Transactions</h2>
            <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                View All Transactions &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">M-Pesa Receipt</th>
                        <th class="px-6 py-3.5">Phone Number</th>
                        <th class="px-6 py-3.5">Obituary Notice</th>
                        <th class="px-6 py-3.5">Amount</th>
                        <th class="px-6 py-3.5">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentPayments as $pay)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">
                                {{ $pay->mpesa_receipt_number }}
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
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $pay->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No payment transactions recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
