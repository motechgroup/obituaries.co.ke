@extends('layouts.advertiser')

@section('title', 'My Campaigns | Advertiser Portal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">My Ad Campaigns</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Track performance, manage statuses, and create targeted banner campaigns.</p>
        </div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center space-x-1.5">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>New Ad Campaign</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        @if($campaigns->isEmpty())
            <div class="text-center py-12 space-y-3 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="material-symbols-outlined text-[48px] text-slate-400">campaign</span>
                <p class="text-base font-bold text-slate-900">No Campaigns Found</p>
                <p class="text-xs text-slate-600 font-medium max-w-sm mx-auto">Create a targeted banner campaign to reach visitors across Kenyan counties.</p>
                <a href="{{ route('advertiser.campaigns.create') }}" class="inline-flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs shadow-sm">
                    <span>Create Campaign &rarr;</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Campaign Name</th>
                            <th class="py-3.5 px-4">Placement Slot</th>
                            <th class="py-3.5 px-4">Dimensions</th>
                            <th class="py-3.5 px-4">Targeting</th>
                            <th class="py-3.5 px-4">Dates & Duration</th>
                            <th class="py-3.5 px-4">Price</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($campaigns as $c)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900 text-sm">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-800 font-semibold">
                                    {{ $c->placement->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] font-semibold text-slate-800">
                                    {{ $c->bannerSize->dimensions ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($c->is_national)
                                        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[10px] uppercase">National (Entire Kenya)</span>
                                    @else
                                        <span class="text-slate-800 font-medium">{{ $c->counties->pluck('county')->join(', ') ?: 'Target Counties' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $c->start_date->format('M d') }} &mdash; {{ $c->end_date->format('M d, Y') }} ({{ $c->total_days }} Days)
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-amber-900 font-mono">
                                    KES {{ number_format($c->calculated_price) }}
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
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="{{ route('advertiser.campaigns.edit', $c->id) }}" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-bold transition-all inline-block shadow-sm">
                                        Edit
                                    </a>
                                    @if($c->status === 'payment_pending')
                                        <a href="{{ route('advertiser.campaigns.checkout', $c->id) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all inline-block shadow-sm">
                                            Pay & Submit &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('advertiser.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all inline-block shadow-sm">
                                            View Details &rarr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-4">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
