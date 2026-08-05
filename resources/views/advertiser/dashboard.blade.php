@extends('layouts.advertiser')

@section('title', 'Advertiser Dashboard | Obituaries.co.ke')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Welcome back, {{ $advertiser->contact_person }}</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">{{ $advertiser->business_name }} &bull; Account Active</p>
        </div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center space-x-1.5">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Create New Ad Campaign</span>
        </a>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Campaigns</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono">{{ $totalCampaignsCount }}</div>
            <p class="text-[11px] text-amber-900 font-bold">{{ $runningCampaignsCount }} Active & Running</p>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Impressions</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-sky-800 font-mono">{{ number_format($totalImpressions) }}</div>
            <p class="text-[11px] text-slate-500 font-medium">Verified Banner Views</p>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Total Clicks</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-800 font-mono">{{ number_format($totalClicks) }}</div>
            <p class="text-[11px] text-slate-500 font-medium">Direct Website Visits</p>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Average CTR</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-800 font-mono">{{ $averageCtr }}%</div>
            <p class="text-[11px] text-slate-500 font-medium">Click Through Ratio</p>
        </div>
    </div>

    <!-- Recent Campaigns Table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-serif text-lg font-bold text-slate-900">Recent Ad Campaigns</h3>
                <p class="text-xs text-slate-600 font-medium">Manage your active, scheduled, and past banner campaigns.</p>
            </div>
            <a href="{{ route('advertiser.campaigns.index') }}" class="text-xs text-amber-900 hover:text-amber-700 font-bold">View All Campaigns &rarr;</a>
        </div>

        @if($recentCampaigns->isEmpty())
            <div class="text-center py-10 space-y-3 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="material-symbols-outlined text-[40px] text-slate-400">campaign</span>
                <p class="text-sm font-semibold text-slate-800">No Advertising Campaigns Created Yet</p>
                <p class="text-xs text-slate-600 font-medium max-w-sm mx-auto">Start marketing your funeral home, hearse fleet, or sympathy flower services to thousands of visitors daily.</p>
                <a href="{{ route('advertiser.campaigns.create') }}" class="inline-flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs shadow-sm">
                    <span>Create Your First Campaign &rarr;</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
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
                    <tbody class="divide-y divide-slate-200">
                        @foreach($recentCampaigns as $c)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $c->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $c->placement->name ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] font-semibold text-slate-800">
                                    {{ $c->bannerSize->dimensions ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($c->is_national)
                                        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[10px] uppercase">Entire Kenya</span>
                                    @else
                                        <span class="text-slate-800 font-semibold">{{ $c->counties->pluck('county')->join(', ') ?: 'County Target' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $c->start_date->format('M d') }} &mdash; {{ $c->end_date->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeClasses = match($c->status) {
                                            'running' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'pending_approval' => 'bg-amber-100 text-amber-900 border-amber-300',
                                            'payment_pending' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            'approved' => 'bg-sky-100 text-sky-800 border-sky-300',
                                            'expired' => 'bg-slate-100 text-slate-600 border-slate-300',
                                            default => 'bg-slate-100 text-slate-700 border-slate-300',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border {{ $badgeClasses }}">
                                        {{ str_replace('_', ' ', $c->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="font-mono text-xs font-bold text-sky-900">{{ number_format($c->impressions_count) }} views</div>
                                    <div class="font-mono text-[11px] text-emerald-800 font-bold">{{ number_format($c->clicks_count) }} clicks ({{ $c->ctr }}%)</div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('advertiser.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 rounded-lg text-xs font-bold transition-all inline-block">
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
