@extends('layouts.admin')

@section('title', 'Ad Analytics & Performance Reports | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Ad Analytics & Performance Reports</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Track banner impressions, click-through rates (CTR), audience device breakdowns, and county performance.</p>
        </div>
        <a href="{{ route('admin.advertising.analytics.export') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center space-x-1.5 shadow-sm transition-all">
            <span class="material-symbols-outlined text-[18px]">download</span>
            <span>Export Analytics Statement (CSV)</span>
        </a>
    </div>

    <!-- Analytics Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 p-5 rounded-2xl space-y-1 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Verified Impressions</span>
            <div class="text-2xl font-extrabold text-slate-900 font-mono">{{ number_format($totalImpressions) }}</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl space-y-1 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Ad Clicks</span>
            <div class="text-2xl font-extrabold text-amber-800 font-mono">{{ number_format($totalClicks) }}</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl space-y-1 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Average Click-Through Rate (CTR)</span>
            <div class="text-2xl font-extrabold text-emerald-700 font-mono">{{ number_format($ctr, 2) }}%</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl space-y-1 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Active Running Banners</span>
            <div class="text-2xl font-extrabold text-sky-800 font-mono">{{ number_format($runningCampaigns) }}</div>
        </div>
    </div>

    <!-- Placement Slot Performance Breakdown -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Performance by Placement Slot</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Placement Slot</th>
                        <th class="py-3 px-4">Page Type</th>
                        <th class="py-3 px-4 text-center">Impressions</th>
                        <th class="py-3 px-4 text-center">Clicks</th>
                        <th class="py-3 px-4 text-right">Click-Through Rate (CTR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($placementsPerformance as $placement)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $placement->name }}
                            </td>
                            <td class="py-3 px-4 uppercase text-slate-600 font-extrabold text-[11px]">
                                {{ $placement->page_type }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-slate-900">
                                {{ number_format($placement->impressions_count) }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-extrabold text-amber-800">
                                {{ number_format($placement->clicks_count) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-extrabold text-emerald-700">
                                {{ number_format($placement->ctr, 2) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Performing Campaigns & Device Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Top Campaigns (lg:col-span-8) -->
        <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Top Performing Ad Campaigns</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Campaign Name</th>
                            <th class="py-3 px-4">Business</th>
                            <th class="py-3 px-4 text-center">Impressions</th>
                            <th class="py-3 px-4 text-center">Clicks</th>
                            <th class="py-3 px-4 text-right">CTR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($topCampaigns as $c)
                            @php
                                $c_ctr = ($c->impressions_count > 0) ? round(($c->clicks_count / $c->impressions_count) * 100, 2) : 0.00;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-semibold">
                                    {{ $c->advertiser->business_name ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono font-bold text-slate-900">
                                    {{ number_format($c->impressions_count) }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono font-extrabold text-amber-800">
                                    {{ number_format($c->clicks_count) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-extrabold text-emerald-700">
                                    {{ number_format($c_ctr, 2) }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audience Devices & County (lg:col-span-4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Device Breakdown -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Device Type Breakdown</h3>
                <div class="space-y-3">
                    @foreach(['Mobile', 'Desktop', 'Tablet'] as $device)
                        @php
                            $count = $impressionsByDevice[$device] ?? 0;
                            $pct = ($totalImpressions > 0) ? round(($count / $totalImpressions) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-800 mb-1">
                                <span>{{ $device }}</span>
                                <span class="font-mono text-slate-900 font-bold">{{ number_format($count) }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top County Breakdown -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Top County Impressions</h3>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach($impressionsByCounty as $co)
                        <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-1.5">
                            <span class="text-slate-800 font-semibold">{{ $co->county }}</span>
                            <span class="font-mono text-amber-900 font-bold">{{ number_format($co->total) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
