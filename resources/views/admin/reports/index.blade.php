@extends('layouts.admin')

@section('title', 'Obituary Reports & Moderation | Admin')

@section('content')
<div class="space-y-6" x-data="{ resolveModal: false, activeReport: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Obituary Reports & Moderation</h1>
            <p class="text-slate-500 text-sm mt-1">Review visitor flag reports, copyright issues, and impersonation claims.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Status Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center space-x-2 overflow-x-auto text-xs font-semibold">
        <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-2 rounded-lg {{ empty($status) ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
            All Reports
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-lg {{ $status === 'pending' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
            Pending
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'system_flagged']) }}" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 {{ $status === 'system_flagged' ? 'bg-amber-900 text-amber-300 font-bold' : 'text-amber-700 hover:bg-amber-50' }}">
            <span>🤖 System Flagged</span>
            @if(!empty($systemFlaggedCount) && $systemFlaggedCount > 0)
                <span class="px-2 py-0.5 bg-rose-600 text-white rounded-full text-[10px] font-bold">{{ $systemFlaggedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="px-3.5 py-2 rounded-lg {{ $status === 'resolved' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
            Resolved
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}" class="px-3.5 py-2 rounded-lg {{ $status === 'dismissed' ? 'bg-slate-900 text-amber-400' : 'text-slate-600 hover:bg-slate-100' }}">
            Dismissed
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'spam']) }}" class="px-3.5 py-2 rounded-lg {{ $status === 'spam' ? 'bg-rose-900 text-rose-300 font-bold' : 'text-rose-700 hover:bg-rose-50' }}">
            Spam & Blocked
        </a>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-3.5">Obituary Notice</th>
                    <th class="px-6 py-3.5">Reason & Details</th>
                    <th class="px-6 py-3.5">Reporter & IP</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($reports as $r)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($r->obituary)
                                <div class="font-bold text-slate-900">{{ $r->obituary->full_name }}</div>
                                <div class="flex items-center space-x-2 mt-1 text-xs">
                                    <a href="{{ route('admin.obituaries.show', $r->obituary_id) }}" class="text-amber-700 font-semibold hover:underline">
                                        Inspect Notice
                                    </a>
                                    <span class="text-slate-300">&bull;</span>
                                    <a href="{{ route('obituaries.show', $r->obituary->slug) }}" target="_blank" class="text-sky-600 font-semibold hover:underline flex items-center space-x-0.5">
                                        <span>View Public</span>
                                        <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                    </a>
                                </div>
                                <span class="text-[11px] text-slate-400 block mt-0.5">Reported {{ $r->created_at->diffForHumans() }}</span>
                            @else
                                <span class="text-slate-400 italic">Deleted Obituary</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <span class="font-bold uppercase tracking-wider text-rose-700 block mb-1">
                                {{ str_replace('_', ' ', $r->reason) }}
                            </span>
                            <p class="text-slate-600 line-clamp-2 max-w-sm font-sans">{{ $r->details }}</p>
                            @if($r->is_system_flagged && $r->resolution_notes)
                                <div class="mt-1.5 text-[11px] font-semibold text-rose-800 bg-rose-50 border border-rose-200 p-1.5 rounded-lg leading-snug">
                                    🤖 {{ $r->resolution_notes }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div class="font-semibold text-slate-900">{{ $r->reporter_name }}</div>
                            <div class="text-slate-500">{{ $r->reporter_email }}</div>
                            @if($r->reporter_phone)
                                <div class="text-slate-500">{{ $r->reporter_phone }}</div>
                            @endif
                            @if($r->ip_address)
                                <div class="inline-block mt-1 font-mono text-[10px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">
                                    IP: {{ $r->ip_address }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($r->is_system_flagged || $r->status === 'flagged_spam')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full text-xs font-bold uppercase flex items-center space-x-1 w-max">
                                    <span>🤖 System Flagged</span>
                                </span>
                            @elseif($r->status === 'pending')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase">Pending</span>
                            @elseif($r->status === 'resolved')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase">Resolved</span>
                            @elseif($r->status === 'spam')
                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-bold uppercase flex items-center space-x-1 w-max">
                                    <span class="material-symbols-outlined text-[14px]">block</span>
                                    <span>Spam</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" @click="activeReport = {{ json_encode($r) }}; resolveModal = true" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold">
                                Review / Resolve
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                            No reports found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-200">
            {{ $reports->links() }}
        </div>
    </div>

    <!-- Resolve Modal -->
    <div x-show="resolveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6" @click.away="resolveModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-serif text-xl font-bold text-slate-900">Resolve Obituary Report #<span x-text="activeReport.id"></span></h3>
                <button type="button" @click="resolveModal = false" class="text-slate-400 font-bold hover:text-slate-600">&times;</button>
            </div>

            <!-- Reported Obituary Live Banner -->
            <template x-if="activeReport.obituary">
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-amber-950 font-bold block text-sm" x-text="activeReport.obituary.full_name"></span>
                        <span class="text-amber-800 text-[11px]">Notice Status: <span class="uppercase font-bold" x-text="activeReport.obituary.status"></span></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a :href="'/obituary/' + activeReport.obituary.slug" target="_blank" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg font-bold flex items-center space-x-1 hover:bg-amber-100 transition-colors">
                            <span>View Public Page</span>
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        </a>
                    </div>
                </div>
            </template>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                <div><strong class="text-slate-700">Reason:</strong> <span class="uppercase font-bold text-rose-700" x-text="activeReport.reason"></span></div>
                <div><strong class="text-slate-700">Report Details:</strong> <p class="mt-1 text-slate-800 whitespace-pre-line font-sans" x-text="activeReport.details"></p></div>
                <div><strong class="text-slate-700">Reporter:</strong> <span x-text="activeReport.reporter_name + ' (' + activeReport.reporter_email + ')'"></span></div>
                <template x-if="activeReport.ip_address">
                    <div><strong class="text-slate-700">IP Address:</strong> <span class="font-mono bg-slate-200 px-1.5 py-0.5 rounded text-[11px]" x-text="activeReport.ip_address"></span></div>
                </template>
            </div>

            <!-- Quick Unpublish Notice Button -->
            <template x-if="activeReport.obituary && activeReport.obituary.status === 'published'">
                <form :action="'/admin/obituaries/' + activeReport.obituary.id + '/unpublish'" method="POST" onsubmit="return confirm('Unpublish this obituary notice immediately from live website?')">
                    @csrf
                    <input type="hidden" name="reason" value="Unpublished during report moderation review.">
                    <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs flex items-center justify-center space-x-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                        <span>Unpublish Obituary Notice Immediately</span>
                    </button>
                </form>
            </template>

            <!-- Quick Mark as Spam & Block IP Button -->
            <form :action="'/admin/reports/' + activeReport.id + '/resolve'" method="POST" onsubmit="return confirm('Mark this report as SPAM and automatically block IP address?')">
                @csrf
                <input type="hidden" name="status" value="spam">
                <input type="hidden" name="resolution_notes" value="Flagged as spam submission. IP automatically blocked.">
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-black text-rose-400 hover:text-rose-300 font-bold rounded-xl text-xs flex items-center justify-center space-x-2 shadow-sm transition-all border border-rose-900/60">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    <span>Mark as Spam & Block Offender IP</span>
                </button>
            </form>

            <form :action="'/admin/reports/' + activeReport.id + '/resolve'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Action Status</label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold">
                        <option value="resolved">Mark as Resolved</option>
                        <option value="reviewed">Mark as Reviewed</option>
                        <option value="dismissed">Dismiss Report</option>
                        <option value="spam">🚫 Mark as Spam & Block IP</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Resolution Notes</label>
                    <textarea name="resolution_notes" rows="3" placeholder="e.g. Contacted submitter or unpublished notice for editorial review." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs"></textarea>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="resolveModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Update Report Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
