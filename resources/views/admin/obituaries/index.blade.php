@extends('layouts.admin')

@section('title', 'Manage Obituaries | Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Obituaries Directory Management</h1>
            <p class="text-slate-500 text-sm mt-1">Filter, inspect, verify, publish, and manually create obituary notices.</p>
        </div>

        <a href="{{ route('admin.obituaries.create') }}" class="px-5 py-2.5 bg-amber-700 hover:bg-amber-800 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center space-x-2 self-start sm:self-auto">
            <span class="text-base font-bold">+</span>
            <span>Add New Obituary (No Payment)</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center space-x-1 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <a href="{{ route('admin.obituaries.index') }}" class="px-3.5 py-2 rounded-lg transition-colors {{ empty($status) ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
                All
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="px-3.5 py-2 rounded-lg transition-colors {{ $status === 'pending_verification' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
                Pending Verification
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'published']) }}" class="px-3.5 py-2 rounded-lg transition-colors {{ $status === 'published' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
                Published
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'rejected']) }}" class="px-3.5 py-2 rounded-lg transition-colors {{ $status === 'rejected' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
                Rejected
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="px-3.5 py-2 rounded-lg transition-colors {{ $status === 'pending_payment' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
                Pending Payment
            </a>
        </div>

        <!-- Search input -->
        <form action="{{ route('admin.obituaries.index') }}" method="GET" class="w-full md:w-72">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name or submitter..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Deceased Name</th>
                        <th class="px-6 py-3.5">Location</th>
                        <th class="px-6 py-3.5">Submitter & Relationship</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Verification</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($obituaries as $ob)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $ob->full_name }}</div>
                                <div class="text-xs text-slate-500">DOD: {{ $ob->date_of_death->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $ob->town }}, {{ $ob->county }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-semibold text-slate-900">{{ $ob->submitter_name }}</div>
                                <div class="text-slate-500">{{ $ob->submitter_phone }} ({{ $ob->relationship }})</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ob->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Published</span>
                                @elseif($ob->status === 'pending_verification')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Pending Verification</span>
                                @elseif($ob->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Rejected</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">{{ ucfirst(str_replace('_', ' ', $ob->status)) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <span class="capitalize font-medium">{{ $ob->verification_status }}</span>
                                @if($ob->verified_at)
                                    <div class="text-[10px] text-slate-400">{{ $ob->verified_at->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.obituaries.show', $ob->id) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors">
                                    Review / Verify
                                </a>
                                <a href="{{ route('admin.obituaries.edit', $ob->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No obituaries found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $obituaries->links() }}
        </div>
    </div>
</div>
@endsection
