@extends('layouts.admin')

@section('title', 'Campaign Review - ' . $campaign->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Campaign Moderation Review</h1>
            <p class="text-xs text-slate-400">Review advertisement parameters, banner dimensions, landing URL, and approve live display.</p>
        </div>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">&larr; Back to Campaigns List</a>
    </div>

    <!-- Moderation Actions Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-white">{{ $campaign->name }}</h3>
                <p class="text-xs text-slate-400">Advertiser: <strong class="text-amber-400">{{ $campaign->advertiser->business_name ?? 'N/A' }}</strong> ({{ $campaign->advertiser->contact_person ?? '' }} &bull; {{ $campaign->advertiser->phone_number ?? '' }})</p>
            </div>
            @php
                $badgeClasses = match($campaign->status) {
                    'running' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                    'pending_approval' => 'bg-amber-950 text-amber-300 border-amber-800 font-bold',
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

        <!-- Banner Preview -->
        <div class="p-4 bg-slate-950 border border-slate-800 rounded-2xl flex flex-col items-center justify-center space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Banner Asset Preview ({{ $campaign->bannerSize->dimensions }})</span>
            <img src="{{ $campaign->banner_url }}" class="max-w-full h-auto rounded-lg shadow-md border border-slate-800">
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-300 p-4 bg-slate-950 rounded-2xl border border-slate-800">
            <div>Placement: <strong class="text-white block font-bold">{{ $campaign->placement->name }}</strong></div>
            <div>Duration: <strong class="text-white block font-bold">{{ $campaign->start_date->format('M d') }} - {{ $campaign->end_date->format('M d, Y') }}</strong></div>
            <div>Targeting: <strong class="text-white block font-bold">{{ $campaign->is_national ? 'Entire Kenya' : ($campaign->counties->pluck('county')->join(', ') ?: 'Target Counties') }}</strong></div>
            <div>Investment: <strong class="text-amber-400 font-mono block font-extrabold">KES {{ number_format($campaign->calculated_price) }}</strong></div>
        </div>

        <!-- Action Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-800">
            @if($campaign->status === 'pending_approval' || $campaign->status === 'approved' || $campaign->status === 'payment_pending')
                <form action="{{ route('admin.advertising.campaigns.approve', $campaign->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Approve this campaign to make it live on the site?')" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-lg flex items-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>Approve Campaign & Make Live</span>
                    </button>
                </form>
            @elseif($campaign->status === 'running')
                <form action="{{ route('admin.advertising.campaigns.pause', $campaign->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-lg">
                        Pause Campaign
                    </button>
                </form>
            @endif

            <!-- Reject Form Modal/Inline -->
            <form action="{{ route('admin.advertising.campaigns.reject', $campaign->id) }}" method="POST" class="flex items-center space-x-2 w-full sm:w-auto">
                @csrf
                <input type="text" name="rejection_reason" required placeholder="Reason for rejection..." class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none w-full sm:w-64">
                <button type="submit" onclick="return confirm('Reject this campaign?')" class="bg-rose-600 hover:bg-rose-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider whitespace-nowrap">
                    Reject Campaign
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
