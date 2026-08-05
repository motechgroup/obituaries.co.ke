@extends('layouts.admin')

@section('title', 'Advertising Campaigns Moderation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Ad Campaigns Moderation</h1>
            <p class="text-xs text-slate-400">Review submitted banner campaigns, verify M-Pesa payments, and approve live advertisements.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.advertising.finance.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 shadow-md">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Revenue & Financials</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-2 text-xs font-semibold">
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-3.5 py-2 rounded-xl transition-all border {{ empty($status) ? 'bg-amber-500/20 text-amber-400 border-amber-500/40 font-bold' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            All Campaigns ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'pending_approval']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'pending_approval' ? 'bg-amber-500/20 text-amber-400 border-amber-500/40 font-bold' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            Pending Approval ({{ $counts['pending_approval'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'running']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'running' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40 font-bold' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            Running ({{ $counts['running'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'payment_pending']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'payment_pending' ? 'bg-rose-500/20 text-rose-400 border-rose-500/40 font-bold' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            Payment Pending ({{ $counts['payment_pending'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'rejected']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'rejected' ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            Rejected ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        @if($campaigns->isEmpty())
            <div class="text-center py-10 text-xs text-slate-400 italic">No campaign records found matching this status.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4">Advertiser & Business</th>
                            <th class="py-3.5 px-4">Campaign Name</th>
                            <th class="py-3.5 px-4">Placement Slot</th>
                            <th class="py-3.5 px-4">Price</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($campaigns as $c)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-white">{{ $c->advertiser->business_name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $c->advertiser->contact_person ?? '' }} &bull; {{ $c->advertiser->phone_number ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-200">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div>{{ $c->placement->name ?? 'N/A' }}</div>
                                    <div class="font-mono text-[10px] text-slate-400">{{ $c->bannerSize->dimensions ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-amber-400 font-mono">
                                    KES {{ number_format($c->calculated_price) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeClasses = match($c->status) {
                                            'running' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                                            'pending_approval' => 'bg-amber-950 text-amber-300 border-amber-800 font-bold animate-pulse',
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
                                    <a href="{{ route('admin.advertising.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition-all inline-block">
                                        Review Details &rarr;
                                    </a>
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
