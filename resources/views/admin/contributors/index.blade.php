@extends('layouts.admin')

@section('title', 'Public Contributors Directory')
@section('header_title', 'Contributors & Submitters Directory')

@section('content')
<div class="space-y-6">

    <!-- Overview Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Unique Submitters</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($totalContributors) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">groups</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Notices Submitted</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($totalSubmissions) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">newspaper</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Average Submissions / User</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">
                    {{ $totalContributors > 0 ? number_format($totalSubmissions / $totalContributors, 1) : '0' }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">analytics</span>
            </div>
        </div>
    </div>

    <!-- Search & Export Header Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="{{ route('admin.contributors.index') }}" method="GET" class="w-full md:w-96 flex items-center space-x-2">
            <div class="relative w-full">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-sm">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, phone, or email..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.contributors.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl text-xs font-medium">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.contributors.export') }}" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center space-x-2 shadow-xs">
            <span class="material-symbols-outlined text-base">download</span>
            <span>Export Contributors CSV</span>
        </a>
    </div>

    <!-- Contributors Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Submitter Name</th>
                        <th class="px-6 py-3.5">Phone Number</th>
                        <th class="px-6 py-3.5">Email Address</th>
                        <th class="px-6 py-3.5 text-center">Total Notices</th>
                        <th class="px-6 py-3.5 text-center">Published / Pending</th>
                        <th class="px-6 py-3.5">Last Active</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($contributors as $contributor)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs uppercase border border-slate-200">
                                        {{ substr($contributor->submitter_name, 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-900 text-sm">{{ $contributor->submitter_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $contributor->submitter_phone }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $contributor->submitter_email ?: '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-full font-bold">
                                    {{ $contributor->total_notices }} Notices
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center space-x-1">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-semibold text-[10px]">
                                    {{ $contributor->published_notices }} Live
                                </span>
                                @if($contributor->pending_notices > 0)
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-semibold text-[10px]">
                                        {{ $contributor->pending_notices }} Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ \Carbon\Carbon::parse($contributor->last_submission_at)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="toggleNoticeDrawer('drawer-{{ $loop->index }}')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg border border-amber-200 text-xs transition-colors inline-flex items-center space-x-1">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <span>Linked Notices ({{ count($contributor->linked_obituaries) }})</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Collapsible Linked Notices Drawer -->
                        <tr id="drawer-{{ $loop->index }}" class="hidden bg-slate-50/50">
                            <td colspan="7" class="px-6 py-4 border-t border-b border-slate-200">
                                <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-3">
                                    <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                                        <span class="material-symbols-outlined text-amber-600 text-base">newspaper</span>
                                        <span>Obituary Notices Submitted by {{ $contributor->submitter_name }}</span>
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($contributor->linked_obituaries as $notice)
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                                                <div>
                                                    <a href="{{ route('admin.obituaries.show', $notice->id) }}" class="font-bold text-amber-900 hover:underline block text-sm">
                                                        {{ $notice->full_name }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-500 block">
                                                        {{ $notice->county }} • Submitted {{ $notice->created_at->format('M d, Y') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    @if($notice->status === 'published')
                                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded text-[10px] uppercase">Published</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded text-[10px] uppercase">{{ str_replace('_', ' ', $notice->status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 italic">
                                No submitters or contributors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contributors->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $contributors->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleNoticeDrawer(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}
</script>
@endsection
