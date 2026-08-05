@extends('layouts.advertiser')

@section('title', 'Campaign Details - ' . $campaign->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-white">{{ $campaign->name }}</h1>
            <p class="text-xs text-slate-400">Campaign ID #AD-{{ $campaign->id }} &bull; {{ $campaign->placement->name }}</p>
        </div>
        <a href="{{ route('advertiser.campaigns.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">&larr; Back to Campaigns</a>
    </div>

    <!-- Status & Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Banner Preview Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Live Banner Preview</h3>
            <div class="p-3 bg-slate-950 border border-slate-800 rounded-2xl flex items-center justify-center min-h-[120px]">
                <img src="{{ $campaign->banner_url }}" class="max-w-full h-auto rounded-lg shadow-md">
            </div>
            <div class="text-xs space-y-1 text-slate-300">
                <div>Dimensions: <strong class="font-mono text-white">{{ $campaign->bannerSize->dimensions }}</strong></div>
                <div>Landing URL: <a href="{{ $campaign->landing_url }}" target="_blank" class="text-sky-400 underline truncate block">{{ $campaign->landing_url }}</a></div>
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="md:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Campaign Performance</h3>
                @php
                    $badgeClasses = match($campaign->status) {
                        'running' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                        'pending_approval' => 'bg-amber-950 text-amber-300 border-amber-800',
                        'payment_pending' => 'bg-rose-950 text-rose-300 border-rose-800',
                        'approved' => 'bg-sky-950 text-sky-300 border-sky-800',
                        'expired' => 'bg-slate-800 text-slate-400 border-slate-700',
                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $badgeClasses }}">
                    {{ str_replace('_', ' ', $campaign->status) }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Impressions</span>
                    <div class="text-2xl font-extrabold text-sky-400">{{ number_format($campaign->impressions_count) }}</div>
                </div>
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Clicks</span>
                    <div class="text-2xl font-extrabold text-emerald-400">{{ number_format($campaign->clicks_count) }}</div>
                </div>
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">CTR</span>
                    <div class="text-2xl font-extrabold text-amber-400">{{ $campaign->ctr }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs text-slate-300 pt-2 border-t border-slate-800">
                <div>Start Date: <strong class="text-white">{{ $campaign->start_date->format('M d, Y') }}</strong></div>
                <div>End Date: <strong class="text-white">{{ $campaign->end_date->format('M d, Y') }}</strong></div>
                <div>Targeting: <strong class="text-white">{{ $campaign->is_national ? 'Entire Kenya' : ($campaign->counties->pluck('county')->join(', ') ?: 'County Target') }}</strong></div>
                <div>Total Investment: <strong class="text-amber-400 font-mono">KES {{ number_format($campaign->calculated_price) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
