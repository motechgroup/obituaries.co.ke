@extends('layouts.admin')

@section('title', 'Advertiser Directory Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Advertiser Accounts Directory</h1>
            <p class="text-xs text-slate-400">Manage registered funeral business advertiser accounts, profile details, and activation statuses.</p>
        </div>
    </div>

    <!-- Search bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
        <form action="{{ route('admin.advertising.advertisers.index') }}" method="GET" class="flex items-center space-x-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by business name, contact person, email, or phone..." class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none">
            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs">Search</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        @if($advertisers->isEmpty())
            <div class="text-center py-8 text-xs text-slate-400 italic">No advertiser accounts found.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Business Name</th>
                            <th class="py-3 px-4">Contact Person</th>
                            <th class="py-3 px-4">Phone / Email</th>
                            <th class="py-3 px-4">Active Campaigns</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($advertisers as $adv)
                            <tr class="hover:bg-slate-800/50">
                                <td class="py-3 px-4 font-bold text-white">
                                    {{ $adv->business_name }}
                                </td>
                                <td class="py-3 px-4 text-slate-200">
                                    {{ $adv->contact_person }}
                                </td>
                                <td class="py-3 px-4 text-slate-400">
                                    <div>{{ $adv->phone_number }}</div>
                                    <div class="text-[11px]">{{ $adv->email }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-amber-400">
                                    {{ $adv->campaigns->where('status', 'running')->count() }} Running
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $adv->status === 'active' ? 'bg-emerald-950 text-emerald-300 border-emerald-800' : 'bg-rose-950 text-rose-300 border-rose-800' }}">
                                        {{ $adv->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('admin.advertising.advertisers.toggle-status', $adv->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded text-xs font-bold">
                                            {{ $adv->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-4">
                {{ $advertisers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
