@extends('layouts.admin')

@section('title', 'Fraud & Threat Prevention Center')
@section('header_title', 'Fraud Detection & Threat Monitoring Center')

@section('content')
<div class="space-y-6">

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Threats</span>
                <div class="text-2xl font-serif font-bold text-amber-600 mt-1">{{ number_format($openAlerts) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">error</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Critical Risk Threats</span>
                <div class="text-2xl font-serif font-bold text-rose-600 mt-1">{{ number_format($criticalAlerts) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">report_problem</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Blocked IP Blacklist</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($totalBlockedIps) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">block</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Scanned Alerts</span>
                <div class="text-2xl font-serif font-bold text-slate-900 mt-1">{{ number_format($totalAlerts) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">security</span>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.fraud.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'open' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Active Threat Alerts ({{ $openAlerts }})
            </a>
            <a href="{{ route('admin.fraud.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Logged Threats ({{ $totalAlerts }})
            </a>
        </div>
    </div>

    <!-- Threat Alerts Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="font-serif text-base font-bold text-slate-900 flex items-center space-x-2">
                <span class="material-symbols-outlined text-amber-600">gavel</span>
                <span>Automated Threat & Fraud Pattern Logs</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Threat Level</th>
                        <th class="px-6 py-3.5">Pattern Type</th>
                        <th class="px-6 py-3.5">Origin IP</th>
                        <th class="px-6 py-3.5">Target Notice / Details</th>
                        <th class="px-6 py-3.5">Timestamp</th>
                        <th class="px-6 py-3.5 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($alerts as $alert)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($alert->risk_level === 'CRITICAL')
                                    <span class="px-3 py-1 bg-rose-100 text-rose-900 font-extrabold rounded-full text-[10px] uppercase border border-rose-300">
                                        ⚡ CRITICAL ({{ $alert->risk_score }}%)
                                    </span>
                                @elseif($alert->risk_level === 'HIGH')
                                    <span class="px-3 py-1 bg-orange-100 text-orange-900 font-extrabold rounded-full text-[10px] uppercase border border-orange-300">
                                        HIGH RISK ({{ $alert->risk_score }}%)
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-amber-100 text-amber-900 font-bold rounded-full text-[10px] uppercase border border-amber-300">
                                        MEDIUM ({{ $alert->risk_score }}%)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $alert->threat_type }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $alert->ip_address }}
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <p class="text-slate-800 text-xs leading-relaxed">{{ $alert->description }}</p>
                                @if($alert->obituary)
                                    <a href="{{ route('admin.obituaries.show', $alert->obituary->id) }}" class="text-[11px] text-amber-700 font-bold hover:underline block mt-1">
                                        Notice #{{ $alert->obituary->id }}: {{ $alert->obituary->full_name }} ({{ $alert->obituary->status }})
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $alert->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                @if($alert->status === 'open')
                                    <form action="{{ route('admin.fraud.block', $alert->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Block IP {{ $alert->ip_address }} and unpublish linked notice?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-lg text-xs shadow-xs">
                                            Block IP & Unpublish
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.fraud.dismiss', $alert->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs">
                                            Dismiss (Safe)
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-bold text-[10px] uppercase rounded-full">
                                        Resolved ({{ $alert->status }})
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">
                                No active threat alerts detected.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alerts->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>

    <!-- Blacklisted IPs Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
        <h3 class="font-serif text-lg font-bold text-slate-900 border-b border-slate-200 pb-3 flex items-center space-x-2">
            <span class="material-symbols-outlined text-rose-600">block</span>
            <span>Blacklisted IP Addresses ({{ count($blockedIps) }})</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($blockedIps as $blocked)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="font-mono font-bold text-slate-900 text-sm block">{{ $blocked->ip_address }}</span>
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ $blocked->reason ?: 'Blocked by admin' }}</span>
                        <span class="text-[10px] text-slate-400 block">Blocked {{ $blocked->created_at->format('M d, Y') }}</span>
                    </div>

                    <form action="{{ route('admin.fraud.unblock', $blocked->id) }}" method="POST" onsubmit="return confirm('Unblock IP {{ $blocked->ip_address }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-lg text-xs transition-colors">
                            Unblock
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic col-span-3">No IP addresses currently blacklisted.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
