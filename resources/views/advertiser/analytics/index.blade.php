@extends('layouts.advertiser')

@section('title', 'Performance Analytics | Advertiser Portal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Performance Analytics & Insights</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Detailed impression trends, click ratios, county breakdowns, and exportable statements.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('advertiser.analytics.export') }}" class="bg-white hover:bg-slate-50 text-slate-900 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center space-x-1.5 border border-slate-300 shadow-xs">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export CSV Report</span>
            </a>
        </div>
    </div>

    <!-- Filter by Campaign -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <form action="{{ route('advertiser.analytics.index') }}" method="GET" class="flex items-center space-x-3 w-full sm:w-auto">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-700">Filter Campaign:</label>
            <select name="campaign_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 outline-none focus:bg-white focus:border-amber-500">
                <option value="">All Active & Past Campaigns</option>
                @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" {{ $selectedCampaignId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </form>

        <div class="flex items-center space-x-6 text-xs text-slate-700 font-semibold">
            <div>Impressions: <strong class="text-sky-800 font-mono font-extrabold">{{ number_format($impressionsCount) }}</strong></div>
            <div>Clicks: <strong class="text-emerald-800 font-mono font-extrabold">{{ number_format($clicksCount) }}</strong></div>
            <div>CTR: <strong class="text-amber-900 font-mono font-extrabold">{{ $ctr }}%</strong></div>
        </div>
    </div>

    <!-- Daily Impressions & Clicks Bar Chart / Data Grid -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Daily Impression & Click Trends (Past 30 Days)</h3>
        <div class="grid grid-cols-6 sm:grid-cols-10 md:grid-cols-15 gap-2 pt-4">
            @foreach(array_slice($dailyData, -15) as $d)
                <div class="flex flex-col items-center justify-end space-y-1 group">
                    <div class="w-full bg-slate-50 rounded-lg p-1 flex flex-col justify-end items-center h-28 border border-slate-200">
                        <div style="height: {{ min(100, $d['impressions'] * 5) }}%" class="w-full bg-sky-500/80 group-hover:bg-sky-600 rounded-t transition-all"></div>
                        <div style="height: {{ min(100, $d['clicks'] * 15) }}%" class="w-full bg-emerald-600 group-hover:bg-emerald-700 rounded-t transition-all"></div>
                    </div>
                    <span class="text-[9px] text-slate-500 font-mono font-bold">{{ $d['date'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex items-center justify-center space-x-6 text-[11px] font-semibold pt-2">
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded bg-sky-500 inline-block"></span> <span class="text-slate-700">Impressions</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded bg-emerald-600 inline-block"></span> <span class="text-slate-700">Clicks</span></span>
        </div>
    </div>

    <!-- Top Counties & Devices -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Top Performing Counties</h3>
            @if($topCounties->isEmpty())
                <p class="text-xs text-slate-500 italic font-medium">No geographic data logged yet.</p>
            @else
                <div class="space-y-3 divide-y divide-slate-100">
                    @foreach($topCounties as $tc)
                        <div class="flex items-center justify-between text-xs pt-2">
                            <span class="text-slate-900 font-semibold">{{ $tc->county }} County</span>
                            <span class="font-mono text-sky-800 font-extrabold">{{ number_format($tc->total) }} views</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Visitor Device Distribution</h3>
            @if($devices->isEmpty())
                <p class="text-xs text-slate-500 italic font-medium">No device data logged yet.</p>
            @else
                <div class="space-y-3 divide-y divide-slate-100">
                    @foreach($devices as $dev)
                        <div class="flex items-center justify-between text-xs pt-2">
                            <span class="text-slate-900 font-semibold">{{ $dev->device_type ?: 'Desktop' }}</span>
                            <span class="font-mono text-emerald-800 font-extrabold">{{ number_format($dev->total) }} views</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
