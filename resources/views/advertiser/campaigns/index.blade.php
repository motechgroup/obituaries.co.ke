@extends('layouts.advertiser')

@section('title', 'My Campaigns')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-white">My Ad Campaigns</h1>
            <p class="text-xs text-slate-400">Track performance, manage statuses, and create targeted banner campaigns.</p>
        </div>
        <a href="{{ route('advertiser.campaigns.create') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center space-x-1.5">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>New Ad Campaign</span>
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        @if($campaigns->isEmpty())
            <div class="text-center py-12 space-y-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
                <span class="material-symbols-outlined text-[48px] text-slate-600">campaign</span>
                <p class="text-base font-bold text-white">No Campaigns Found</p>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Create a targeted banner campaign to reach visitors across Kenyan counties.</p>
                <a href="{{ route('advertiser.campaigns.create') }}" class="inline-flex items-center space-x-1.5 bg-amber-500 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs">
                    <span>Create Campaign &rarr;</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800">
                        @foreach($campaigns as $c)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-white">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-300 font-semibold">
                                    {{ $c->placement->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    {{ $c->bannerSize->dimensions ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($c->is_national)
                                        <span class="px-2 py-0.5 rounded-full bg-sky-950 text-sky-400 border border-sky-800 font-bold text-[10px] uppercase">National (Entire Kenya)</span>
                                    @else
                                        <span class="text-slate-300 font-medium">{{ $c->counties->pluck('county')->join(', ') ?: 'Target Counties' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $c->start_date->format('M d') }} &mdash; {{ $c->end_date->format('M d, Y') }} ({{ $c->total_days }} Days)
                                </td>
                                <td class="py-3.5 px-4 font-bold text-amber-400">
                                    KES {{ number_format($c->calculated_price) }}
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
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="{{ route('advertiser.campaigns.edit', $c->id) }}" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg text-xs font-bold transition-all inline-block">
                                        Edit
                                    </a>
                                    @if($c->status === 'payment_pending')
                                        <a href="{{ route('advertiser.campaigns.checkout', $c->id) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xs font-bold transition-all inline-block">
                                            Pay & Submit &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('advertiser.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition-all inline-block">
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
