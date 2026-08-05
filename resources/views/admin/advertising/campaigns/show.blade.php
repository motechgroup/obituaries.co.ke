@extends('layouts.admin')

@section('title', 'Campaign Review - ' . $campaign->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Campaign Moderation Review</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Review advertisement parameters, banner dimensions, landing URL, and approve live display.</p>
        </div>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Back to Campaigns List</a>
    </div>

    <!-- Moderation Actions Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-slate-900">{{ $campaign->name }}</h3>
                <p class="text-xs text-slate-600 font-medium">Advertiser: <strong class="text-amber-900">{{ $campaign->advertiser->business_name ?? 'N/A' }}</strong> ({{ $campaign->advertiser->contact_person ?? '' }} &bull; {{ $campaign->advertiser->phone_number ?? '' }})</p>
            </div>
            @php
                $badgeClasses = match($campaign->status) {
                    'running' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'pending_approval' => 'bg-amber-100 text-amber-900 border-amber-300 font-bold',
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

        <!-- Banner Preview -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col items-center justify-center space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Banner Asset Preview ({{ $campaign->bannerSize->dimensions }})</span>
            <img src="{{ $campaign->banner_url }}" class="max-w-full h-auto rounded-lg shadow-sm border border-slate-200">
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-700 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div>Placement: <strong class="text-slate-900 block font-bold">{{ $campaign->placement->name }}</strong></div>
            <div>Duration: <strong class="text-slate-900 block font-bold">{{ $campaign->start_date->format('M d') }} - {{ $campaign->end_date->format('M d, Y') }}</strong></div>
            <div>Targeting: <strong class="text-slate-900 block font-bold">{{ $campaign->is_national ? 'Entire Kenya' : ($campaign->counties->pluck('county')->join(', ') ?: 'Target Counties') }}</strong></div>
            <div>Investment: <strong class="text-amber-900 font-mono block font-extrabold">KES {{ number_format($campaign->calculated_price) }}</strong></div>
        </div>

        <!-- Action Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200">
            @if($campaign->status === 'pending_approval' || $campaign->status === 'approved' || $campaign->status === 'payment_pending')
                <form action="{{ route('admin.advertising.campaigns.approve', $campaign->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Approve this campaign to make it live on the site?')" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-sm flex items-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>Approve Campaign & Make Live</span>
                    </button>
                </form>
            @elseif($campaign->status === 'running')
                <form action="{{ route('admin.advertising.campaigns.pause', $campaign->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-sm">
                        Pause Campaign
                    </button>
                </form>
            @endif

            <!-- Reject Form Modal/Inline -->
            <form action="{{ route('admin.advertising.campaigns.reject', $campaign->id) }}" method="POST" class="flex items-center space-x-2 w-full sm:w-auto">
                @csrf
                <input type="text" name="rejection_reason" required placeholder="Reason for rejection..." class="px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 outline-none w-full sm:w-64 focus:bg-white focus:border-amber-500">
                <button type="submit" onclick="return confirm('Reject this campaign?')" class="bg-rose-700 hover:bg-rose-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider whitespace-nowrap">
                    Reject Campaign
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
