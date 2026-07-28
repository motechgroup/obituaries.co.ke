@extends('layouts.admin')

@section('title', 'Security Audit Logs')
@section('header_title', 'Security Audit Logs & IP Tracking')

@section('content')
<div class="space-y-6">

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Recorded Logs</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($totalLogs) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">shield</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Threat Flags / Warnings</span>
                <div class="text-2xl font-serif font-bold text-rose-600 mt-1">{{ number_format($warningCount) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Unique IP Addresses</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($uniqueIps) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">public</span>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.security-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-center">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Search IP Address</label>
                <input type="text" name="ip" value="{{ $searchIp }}" placeholder="e.g. 197.232.12.4" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Action Event</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Actions</option>
                    <option value="obituary_submitted" {{ $action === 'obituary_submitted' ? 'selected' : '' }}>Obituary Submitted</option>
                    <option value="fraud_pattern_detected" {{ $action === 'fraud_pattern_detected' ? 'selected' : '' }}>Fraud Threat Detected</option>
                    <option value="ip_blocked" {{ $action === 'ip_blocked' ? 'selected' : '' }}>IP Blocked</option>
                    <option value="admin_login" {{ $action === 'admin_login' ? 'selected' : '' }}>Admin Login</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Severity Level</label>
                <select name="severity" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Severities</option>
                    <option value="info" {{ $severity === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ $severity === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ $severity === 'danger' ? 'selected' : '' }}>Danger</option>
                    <option value="critical" {{ $severity === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="flex items-end space-x-2 pt-4">
                <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition-all shadow-xs">
                    Filter Logs
                </button>
                @if($searchIp || $action || $severity)
                    <a href="{{ route('admin.security-logs.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl text-xs font-semibold">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Security Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Timestamp</th>
                        <th class="px-6 py-3.5">IP Address</th>
                        <th class="px-6 py-3.5">Device Type & Browser</th>
                        <th class="px-6 py-3.5">Action Event</th>
                        <th class="px-6 py-3.5">Severity</th>
                        <th class="px-6 py-3.5">Details / Notice</th>
                        <th class="px-6 py-3.5 text-right">Security Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($log->device_type === 'Mobile')
                                        <span class="material-symbols-outlined text-base text-slate-500">smartphone</span>
                                    @elseif($log->device_type === 'Tablet')
                                        <span class="material-symbols-outlined text-base text-slate-500">tablet</span>
                                    @else
                                        <span class="material-symbols-outlined text-base text-slate-500">computer</span>
                                    @endif
                                    <span class="font-bold text-slate-800">{{ $log->device_type }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 block truncate max-w-xs" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 40) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 uppercase text-[11px]">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->severity === 'danger' || $log->severity === 'critical')
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold rounded-full text-[10px] uppercase">
                                        {{ $log->severity }}
                                    </span>
                                @elseif($log->severity === 'warning')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-full text-[10px] uppercase">
                                        Warning
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-full text-[10px] uppercase">
                                        Info
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-slate-800 text-xs">{{ $log->details ?: '—' }}</p>
                                @if($log->obituary)
                                    <a href="{{ route('admin.obituaries.show', $log->obituary->id) }}" class="text-[11px] text-amber-700 font-bold hover:underline block mt-1">
                                        Notice #{{ $log->obituary->id }}: {{ $log->obituary->full_name }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.security-logs.block-ip') }}" method="POST" onsubmit="return confirm('Block IP {{ $log->ip_address }} from submitting or accessing the website?')">
                                    @csrf
                                    <input type="hidden" name="ip_address" value="{{ $log->ip_address }}">
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg border border-rose-200 text-xs transition-colors inline-flex items-center space-x-1">
                                        <span class="material-symbols-outlined text-sm">block</span>
                                        <span>Block IP</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 italic">
                                No security logs recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
