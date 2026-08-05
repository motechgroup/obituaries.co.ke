@extends('layouts.admin')

@section('title', 'Advertising Campaigns Moderation | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Ad Campaigns Moderation</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Review submitted banner campaigns, verify M-Pesa payments, or manually create and place ad banners.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.advertising.campaigns.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold rounded-xl text-xs flex items-center space-x-1.5 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>+ Create & Place Ad</span>
            </a>
            <a href="{{ route('admin.advertising.finance.index') }}" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Revenue & Financials</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-2 text-xs font-semibold">
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-3.5 py-2 rounded-xl transition-all border {{ empty($status) ? 'bg-amber-500/20 text-amber-900 border-amber-500/40 font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            All Campaigns ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'pending_approval']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'pending_approval' ? 'bg-amber-500/20 text-amber-900 border-amber-500/40 font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Pending Approval ({{ $counts['pending_approval'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'running']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'running' ? 'bg-emerald-500/20 text-emerald-900 border-emerald-500/40 font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Running ({{ $counts['running'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'payment_pending']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'payment_pending' ? 'bg-rose-500/20 text-rose-900 border-rose-500/40 font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Payment Pending ({{ $counts['payment_pending'] }})
        </a>
        <a href="{{ route('admin.advertising.campaigns.index', ['status' => 'rejected']) }}" class="px-3.5 py-2 rounded-xl transition-all border {{ $status === 'rejected' ? 'bg-slate-200 text-slate-800 border-slate-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Rejected ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        @if($campaigns->isEmpty())
            <div class="text-center py-10 text-xs text-slate-500 italic">No campaign records found matching this status.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Advertiser & Business</th>
                            <th class="py-3.5 px-4">Campaign Name</th>
                            <th class="py-3.5 px-4">Placement Slot</th>
                            <th class="py-3.5 px-4">Price</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($campaigns as $c)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $c->advertiser->business_name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $c->advertiser->contact_person ?? '' }} &bull; {{ $c->advertiser->phone_number ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-900">{{ $c->placement->name ?? 'N/A' }}</div>
                                    <div class="font-mono text-[10px] text-slate-500">{{ $c->bannerSize->dimensions ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-amber-900 font-mono">
                                    KES {{ number_format($c->calculated_price) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeClasses = match($c->status) {
                                            'running' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'pending_approval' => 'bg-amber-100 text-amber-900 border-amber-300 font-bold animate-pulse',
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
                                    <a href="{{ route('admin.advertising.campaigns.edit', $c->id) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-bold transition-all inline-block">
                                        Edit
                                    </a>
                                    <a href="{{ route('admin.advertising.campaigns.show', $c->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all inline-block">
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
