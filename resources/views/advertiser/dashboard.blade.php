@extends('layouts.advertiser')

@section('title', 'Advertiser Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-amber-950/40 to-slate-900 p-6 rounded-3xl border border-slate-800 shadow-xl">
        <div class="space-y-1">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-white">Welcome back, {{ $advertiser->contact_person }}</h1>
            <p class="text-xs text-slate-300">{{ $advertiser->business_name }} &bull; Account Active</p>
        </div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-1.5">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Create New Ad Campaign</span>
        </a>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Campaigns</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ $totalCampaignsCount }}</div>
            <p class="text-[11px] text-amber-400 font-medium">{{ $runningCampaignsCount }} Active & Running</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Impressions</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-sky-400">{{ number_format($totalImpressions) }}</div>
            <p class="text-[11px] text-slate-400">Verified Banner Views</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Clicks</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ number_format($totalClicks) }}</div>
            <p class="text-[11px] text-slate-400">Direct Website Visits</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Average CTR</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-400">{{ $averageCtr }}%</div>
            <p class="text-[11px] text-slate-400">Click Through Ratio</p>
        </div>
    </div>

    <!-- Recent Campaigns Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="font-serif text-lg font-bold text-white">Recent Ad Campaigns</h3>
                <p class="text-xs text-slate-400">Manage your active, scheduled, and past banner campaigns.</p>
            </div>
            <a href="{{ route('advertiser.campaigns.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-bold">View All Campaigns &rarr;</a>
        </div>

        @if($recentCampaigns->isEmpty())
            <div class="text-center py-10 space-y-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
                <span class="material-symbols-outlined text-[40px] text-slate-600">campaign</span>
                <p class="text-sm font-semibold text-slate-300">No Advertising Campaigns Created Yet</p>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Start marketing your funeral home, hearse fleet, or sympathy flower services to thousands of visitors daily.</p>
                <a href="{{ route('advertiser.campaigns.create') }}" class="inline-flex items-center space-x-1.5 bg-amber-500 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs">
                    <span>Create Your First Campaign &rarr;</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4">Campaign & Placement</th>
                            <th class="py-3.5 px-4">Size & Format</th>
                            <th class="py-3.5 px-4">Targeting</th>
                            <th class="py-3.5 px-4">Duration</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Performance</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($recentCampaigns as $c)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-white text-sm">{{ $c->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $c->placement->name ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    {{ $c->bannerSize->dimensions ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($c->is_national)
                                        <span class="px-2 py-0.5 rounded-full bg-sky-950 text-sky-400 border border-sky-800 font-bold text-[10px] uppercase">Entire Kenya</span>
                                    @else
                                        <span class="text-slate-300 font-semibold">{{ $c->counties->pluck('county')->join(', ') ?: 'County Target' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $c->start_date->format('M d') }} &mdash; {{ $c->end_date->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeClasses = match($c->status) {
                                            'running' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                                            'pending_approval' => 'bg-amber-950 text-amber-300 border-amber-800',
                                            'payment_pending' => 'bg-rose-950 text-rose-300 border-rose-800',
                                            'approved' => 'bg-sky-950 text-sky-300 border-sky-800',
                                            'expired' => 'bg-slate-800 text-slate-400 border-slate-700',
                                            default => 'bg-slate-800 text-slate-300 border-slate-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeClasses }}">
                                        {{ str_replace('_', ' ', $c->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="font-mono text-xs font-bold text-sky-400">{{ number_format($c->impressions_count) }} views</div>
                                    <div class="font-mono text-[11px] text-emerald-400">{{ number_format($c->clicks_count) }} clicks ({{ $c->ctr }}%)</div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('advertiser.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition-all inline-block">
                                        Details &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
