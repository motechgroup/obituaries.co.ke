@extends('layouts.advertiser')

@section('title', 'Campaign Details - ' . $campaign->name . ' | Advertiser Portal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">{{ $campaign->name }}</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Campaign ID #AD-{{ $campaign->id }} &bull; {{ $campaign->placement->name ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('advertiser.campaigns.edit', $campaign->id) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs flex items-center space-x-1.5 shadow-sm transition-all">
                <span>Edit Campaign</span>
            </a>
            <a href="{{ route('advertiser.campaigns.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
                &larr; Back to Campaigns
            </a>
        </div>
    </div>

    <!-- Status & Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Banner Preview Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Live Banner Preview</h3>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center min-h-[120px]">
                <img src="{{ $campaign->banner_url }}" class="max-w-full h-auto rounded-lg shadow-sm border border-slate-200 bg-white">
            </div>
            <div class="text-xs space-y-1 text-slate-700 font-medium">
                <div>Dimensions: <strong class="font-mono text-slate-900 font-bold">{{ $campaign->bannerSize->dimensions ?? 'N/A' }}</strong></div>
                <div>Landing URL: <a href="{{ $campaign->landing_url }}" target="_blank" class="text-sky-800 font-bold underline truncate block">{{ $campaign->landing_url ?: 'Display-only (No Redirect)' }}</a></div>
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="md:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 space-y-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Campaign Performance</h3>
                @php
                    $badgeClasses = match($campaign->status) {
                        'running' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'pending_approval' => 'bg-amber-100 text-amber-900 border-amber-300',
                        'payment_pending' => 'bg-rose-100 text-rose-800 border-rose-300',
                        'approved' => 'bg-sky-100 text-sky-800 border-sky-300',
                        'expired' => 'bg-slate-100 text-slate-600 border-slate-300',
                        default => 'bg-slate-100 text-slate-700 border-slate-300',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider border {{ $badgeClasses }}">
                    {{ str_replace('_', ' ', $campaign->status) }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Impressions</span>
                    <div class="text-2xl font-extrabold text-sky-800 font-mono">{{ number_format($campaign->impressions_count) }}</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Clicks</span>
                    <div class="text-2xl font-extrabold text-emerald-800 font-mono">{{ number_format($campaign->clicks_count) }}</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">CTR</span>
                    <div class="text-2xl font-extrabold text-amber-900 font-mono">{{ $campaign->ctr }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs text-slate-700 font-medium pt-2 border-t border-slate-100">
                <div>Start Date: <strong class="text-slate-900 font-bold">{{ $campaign->start_date->format('M d, Y') }}</strong></div>
                <div>End Date: <strong class="text-slate-900 font-bold">{{ $campaign->end_date->format('M d, Y') }}</strong></div>
                <div>Targeting: <strong class="text-slate-900 font-bold">{{ $campaign->is_national ? 'Entire Kenya' : ($campaign->counties->pluck('county')->join(', ') ?: 'County Target') }}</strong></div>
                <div>Total Investment: <strong class="text-amber-900 font-mono font-extrabold">KES {{ number_format($campaign->calculated_price) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
