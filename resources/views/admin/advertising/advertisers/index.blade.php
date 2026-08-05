@extends('layouts.admin')

@section('title', 'Advertiser Directory Management | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Advertiser Accounts Directory</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Manage registered funeral business advertiser accounts, contact info, profile details, and activation status.</p>
        </div>
        <a href="{{ route('admin.advertising.advertisers.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center space-x-1.5">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Register New Advertiser</span>
        </a>
    </div>

    <!-- Search bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('admin.advertising.advertisers.index') }}" method="GET" class="flex items-center space-x-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by business name, contact person, email, or phone..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 outline-none focus:bg-white focus:border-amber-500">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-sm">Search</button>
            @if(!empty($search))
                <a href="{{ route('admin.advertising.advertisers.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Clear</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        @if($advertisers->isEmpty())
            <div class="text-center py-8 text-xs text-slate-500 italic">No advertiser accounts found matching your query.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Business Name</th>
                            <th class="py-3 px-4">Contact Person</th>
                            <th class="py-3 px-4">Phone / Email</th>
                            <th class="py-3 px-4">Campaigns</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($advertisers as $adv)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-900 flex items-center justify-center font-extrabold text-xs">
                                            {{ strtoupper(substr($adv->business_name, 0, 1)) }}
                                        </span>
                                        <span>{{ $adv->business_name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-800 font-semibold">
                                    {{ $adv->contact_person }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-bold text-slate-900">{{ $adv->phone_number }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $adv->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900">
                                    <span class="text-amber-800">{{ $adv->campaigns->where('status', 'running')->count() }} Running</span>
                                    <span class="text-slate-400 font-normal"> / {{ $adv->campaigns->count() }} Total</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border {{ $adv->status === 'active' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300' }}">
                                        {{ $adv->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5">
                                    <a href="{{ route('admin.advertising.advertisers.show', $adv->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition-all">
                                        View
                                    </a>
                                    <a href="{{ route('admin.advertising.advertisers.edit', $adv->id) }}" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-bold transition-all">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.advertising.advertisers.toggle-status', $adv->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 {{ $adv->status === 'active' ? 'bg-rose-100 hover:bg-rose-200 text-rose-800' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800' }} rounded-lg text-xs font-bold transition-all">
                                            {{ $adv->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.advertising.advertisers.destroy', $adv->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this advertiser account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all">
                                            Delete
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
