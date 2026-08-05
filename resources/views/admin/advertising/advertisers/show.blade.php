@extends('layouts.admin')

@section('title', 'Advertiser Details | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div class="flex items-center space-x-3">
            <span class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-900 flex items-center justify-center font-extrabold text-xl">
                {{ strtoupper(substr($advertiser->business_name, 0, 1)) }}
            </span>
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">{{ $advertiser->business_name }}</h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600">Registered Advertiser Account • {{ $advertiser->email }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.advertising.advertisers.edit', $advertiser->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition-all shadow-sm">
                Edit Account
            </a>
            <form action="{{ route('admin.advertising.advertisers.toggle-status', $advertiser->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 {{ $advertiser->status === 'active' ? 'bg-rose-100 hover:bg-rose-200 text-rose-800' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800' }} rounded-xl text-xs font-bold transition-all">
                    {{ $advertiser->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                </button>
            </form>
            <a href="{{ route('admin.advertising.advertisers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Account Overview Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Account Details</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 block">Contact Person</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $advertiser->contact_person }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Phone Number</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $advertiser->phone_number }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Email Address</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $advertiser->email }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Account Status</span>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border {{ $advertiser->status === 'active' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300' }}">
                        {{ $advertiser->status }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Registered On</span>
                    <span class="font-medium text-slate-700">{{ $advertiser->created_at->format('M d, Y - g:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Business Profiles List -->
        <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Associated Business Profiles ({{ $advertiser->profiles->count() }})</h3>

            @if($advertiser->profiles->isEmpty())
                <div class="text-xs text-slate-500 italic py-4">No business profiles setup yet.</div>
            @else
                <div class="space-y-3">
                    @foreach($advertiser->profiles as $p)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">{{ $p->business_name }}</span>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-700">{{ $p->category?->name ?? 'General Business' }}</span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $p->description ?: 'No detailed description provided.' }}</p>
                            <div class="text-[11px] text-slate-500 font-medium">📍 {{ $p->address ?: 'N/A' }} • County: {{ $p->county ?: 'National' }} • 📞 {{ $p->phone }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ad Campaigns History ({{ $advertiser->campaigns->count() }})</h3>

        @if($advertiser->campaigns->isEmpty())
            <div class="text-xs text-slate-500 italic py-4">No campaigns created yet by this advertiser.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Campaign Name</th>
                            <th class="py-3 px-4">Placement & Size</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Cost</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($advertiser->campaigns as $camp)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $camp->name }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $camp->placement?->name }} ({{ $camp->bannerSize?->dimensions }})</td>
                                <td class="py-3 px-4 text-slate-600">{{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }} ({{ $camp->total_days }} days)</td>
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">KES {{ number_format($camp->calculated_price, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $camp->status === 'running' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                        {{ $camp->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.advertising.campaigns.show', $camp->id) }}" class="px-2.5 py-1 bg-slate-900 text-white rounded text-xs font-bold">Manage Campaign</a>
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
