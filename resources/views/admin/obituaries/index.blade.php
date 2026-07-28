@extends('layouts.admin')

@section('title', 'Obituaries Directory Management | Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Obituaries Directory Management</h1>
            <p class="text-slate-500 text-xs mt-1">Manage, inspect, verify, edit, and publish obituary notices across Kenya.</p>
        </div>

        <a href="{{ route('admin.obituaries.create') }}" style="background-color: #b45309 !important; color: #ffffff !important;" class="px-6 py-3 bg-amber-700 hover:bg-amber-800 text-white rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center space-x-2 self-start sm:self-auto border border-amber-800">
            <span class="text-amber-300 text-base font-black">+</span>
            <span class="text-white font-extrabold">Add New Obituary</span>
            @if(Auth::guard('admin')->user()->isSuperAdmin())
                <span class="text-[10px] bg-amber-900/60 px-1.5 py-0.5 rounded font-mono text-amber-200 border border-amber-500/30">Free</span>
            @else
                <span class="text-[10px] bg-amber-900/60 px-1.5 py-0.5 rounded font-mono text-amber-200 border border-amber-500/30">M-Pesa</span>
            @endif
        </a>
    </div>

    <!-- Quick Overview Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.obituaries.index') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-amber-300 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Notices</span>
                <div class="text-xl font-serif font-bold text-slate-900 mt-0.5">{{ \App\Models\Obituary::count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">newspaper</span>
            </div>
        </a>

        <a href="{{ route('admin.obituaries.index', ['status' => 'published']) }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-emerald-300 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Live Published</span>
                <div class="text-xl font-serif font-bold text-emerald-700 mt-0.5">{{ \App\Models\Obituary::where('status', 'published')->count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">check_circle</span>
            </div>
        </a>

        <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-amber-400 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending Verification</span>
                <div class="text-xl font-serif font-bold text-amber-700 mt-0.5">{{ \App\Models\Obituary::where('status', 'pending_verification')->count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">hourglass_top</span>
            </div>
        </a>

        <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-blue-400 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending Payment</span>
                <div class="text-xl font-serif font-bold text-slate-900 mt-0.5">{{ \App\Models\Obituary::where('status', 'pending_payment')->count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">payments</span>
            </div>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto w-full md:w-auto text-xs font-semibold pb-1 md:pb-0">
            <a href="{{ route('admin.obituaries.index') }}" class="px-4 py-2 rounded-xl transition-all {{ empty($status) ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Notices
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'pending_verification' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Pending Verification
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'published' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Published
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'rejected' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Rejected
            </a>
            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="px-4 py-2 rounded-xl transition-all {{ $status === 'pending_payment' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Pending Payment
            </a>
        </div>

        <!-- Search Input Form -->
        <form action="{{ route('admin.obituaries.index') }}" method="GET" class="w-full md:w-80 flex items-center space-x-2">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative w-full">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-sm">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, submitter or phone..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition-colors">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.obituaries.index', ['status' => $status]) }}" class="px-2.5 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl text-xs font-semibold">Clear</a>
            @endif
        </form>
    </div>

    <!-- Clickable Obituaries Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Deceased Notice Details</th>
                        <th class="px-6 py-3.5">Location</th>
                        <th class="px-6 py-3.5">Submitter & Contact</th>
                        <th class="px-6 py-3.5">Notice Status</th>
                        <th class="px-6 py-3.5">Verification</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($obituaries as $ob)
                        <tr onclick="if(!event.target.closest('button, a, form')){ window.location='{{ route('admin.obituaries.show', $ob->id) }}'; }" class="hover:bg-amber-50/60 transition-colors cursor-pointer group">
                            
                            <!-- Deceased Photo & Name Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- Photo Thumbnail Avatar -->
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center font-bold text-slate-600 text-xs">
                                        @if($ob->photo)
                                            <img src="{{ asset('storage/' . $ob->photo) }}" alt="{{ $ob->full_name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-400 text-xl">person</span>
                                        @endif
                                    </div>

                                    <div>
                                        <a href="{{ route('admin.obituaries.show', $ob->id) }}" class="font-serif font-bold text-slate-900 text-sm group-hover:text-amber-900 hover:underline block leading-tight">
                                            {{ $ob->full_name }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            Passed: {{ $ob->date_of_death->format('M d, Y') }}
                                            @if($ob->age) &bull; {{ $ob->age }} Yrs @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Location Column -->
                            <td class="px-6 py-4 text-slate-700">
                                <span class="font-bold text-slate-900 block text-xs">{{ $ob->county }}</span>
                                <span class="text-[10px] text-slate-500 block">{{ $ob->town }}</span>
                            </td>

                            <!-- Submitter & Contact Column -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 block">{{ $ob->submitter_name }}</span>
                                <span class="text-[10px] font-mono text-slate-600 block">{{ $ob->submitter_phone }} ({{ $ob->relationship }})</span>
                            </td>

                            <!-- Publication Status Column -->
                            <td class="px-6 py-4">
                                @if($ob->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Published
                                    </span>
                                @elseif($ob->status === 'pending_verification')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        Pending Verification
                                    </span>
                                @elseif($ob->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                        {{ ucfirst(str_replace('_', ' ', $ob->status)) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Verification Status Column -->
                            <td class="px-6 py-4 text-slate-600">
                                <span class="capitalize font-bold text-slate-800 block text-xs">{{ $ob->verification_status }}</span>
                                @if($ob->verified_at)
                                    <span class="text-[10px] text-slate-400 block">{{ $ob->verified_at->format('M d, Y') }}</span>
                                @endif
                            </td>

                            <!-- Clean Action Buttons Column -->
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap" onclick="event.stopPropagation();">
                                <!-- Manage / View Details Button -->
                                <a href="{{ route('admin.obituaries.show', $ob->id) }}" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-900 rounded-lg border border-amber-200 text-xs font-bold transition-colors inline-flex items-center justify-center" title="Manage Obituary Notice">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>

                                <!-- Edit Notice Button -->
                                <a href="{{ route('admin.obituaries.edit', $ob->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg border border-slate-200 text-xs font-bold transition-colors inline-flex items-center justify-center" title="Edit Obituary Details">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </a>

                                <!-- View Live Button if published -->
                                @if($ob->status === 'published')
                                    <a href="{{ route('obituaries.show', $ob->slug) }}" target="_blank" class="p-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg border border-emerald-200 text-xs font-bold transition-colors inline-flex items-center justify-center" title="View Public Live Page">
                                        <span class="material-symbols-outlined text-base">open_in_new</span>
                                    </a>
                                @endif

                                <!-- Delete Notice Button -->
                                <form action="{{ route('admin.obituaries.destroy', $ob->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete obituary notice for {{ e($ob->full_name) }}? This cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg border border-rose-200 text-xs font-bold transition-colors inline-flex items-center justify-center" title="Delete Obituary Notice">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No obituaries found matching the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($obituaries->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $obituaries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
