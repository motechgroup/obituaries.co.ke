@extends('layouts.admin')

@section('title', 'Traffic Analytics & Audience Insights | Admin')

@section('content')
<div class="space-y-8">
    <!-- Header & Range Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Traffic Analytics & Audience Insights</h1>
            <p class="text-slate-500 text-sm mt-1">Real-time visitor counts, traffic sources, top viewed obituary notices, and device breakdowns.</p>
        </div>

        <div class="bg-white rounded-xl p-1.5 border border-slate-200 shadow-xs flex items-center space-x-1 text-xs font-semibold">
            <a href="{{ route('admin.analytics.index', ['range' => 'today']) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $range === 'today' ? 'bg-slate-900 text-amber-400 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                Today
            </a>
            <a href="{{ route('admin.analytics.index', ['range' => '7_days']) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $range === '7_days' ? 'bg-slate-900 text-amber-400 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                7 Days
            </a>
            <a href="{{ route('admin.analytics.index', ['range' => '30_days']) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $range === '30_days' ? 'bg-slate-900 text-amber-400 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                30 Days
            </a>
            <a href="{{ route('admin.analytics.index', ['range' => 'year']) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $range === 'year' ? 'bg-slate-900 text-amber-400 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                All Time
            </a>
        </div>
    </div>

    <!-- Analytics Stat Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Page Views -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Page Views</span>
                <span class="font-serif text-3xl font-bold text-slate-900">{{ number_format($totalPageViews) }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">visibility</span>
            </div>
        </div>

        <!-- Unique Visitors -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Unique Visitors</span>
                <span class="font-serif text-3xl font-bold text-slate-900">{{ number_format($uniqueVisitors) }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">groups</span>
            </div>
        </div>

        <!-- Mobile Visitor Share -->
        @php
            $mobileCount = $deviceBreakdown['mobile'] ?? 0;
            $mobileShare = $totalPageViews > 0 ? round(($mobileCount / $totalPageViews) * 100, 1) : 0;
        @endphp
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Mobile Visitor Share</span>
                <span class="font-serif text-3xl font-bold text-slate-900">{{ $mobileShare }}%</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">{{ number_format($mobileCount) }} mobile views</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">smartphone</span>
            </div>
        </div>

        <!-- Direct vs Referred -->
        @php
            $directCount = 0;
            foreach($referrers as $ref) {
                if ($ref->referer_host === 'Direct / Bookmark') $directCount = $ref->count;
            }
            $referredShare = $totalPageViews > 0 ? round((($totalPageViews - $directCount) / $totalPageViews) * 100, 1) : 0;
        @endphp
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">External Referrals</span>
                <span class="font-serif text-3xl font-bold text-slate-900">{{ $referredShare }}%</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Google, WhatsApp, Social</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[24px]">share</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Top Obituaries & Traffic Sources -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top Viewed Obituaries (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg font-bold text-slate-900">Top Viewed Obituary Notices</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Most read death announcements and memorial tributes.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Obituary Notice</th>
                            <th class="px-6 py-3.5">County & Town</th>
                            <th class="px-6 py-3.5 text-right">Total Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($topObituaries as $item)
                            @if($item->obituary)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            @if($item->obituary->photo)
                                                <img src="{{ asset('storage/' . $item->obituary->photo) }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-slate-200">
                                            @else
                                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs flex-shrink-0">
                                                    ✝
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.obituaries.show', $item->obituary->id) }}" class="font-bold text-slate-900 hover:text-amber-700 text-xs block">
                                                    {{ $item->obituary->full_name }}
                                                </a>
                                                <a href="{{ route('obituaries.show', $item->obituary->slug) }}" target="_blank" class="text-[11px] text-sky-600 hover:underline">
                                                    View Public Page &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                        {{ $item->obituary->town }}, {{ $item->obituary->county }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-3 py-1 bg-amber-50 text-amber-900 font-serif font-bold text-sm rounded-lg border border-amber-200">
                                            {{ number_format($item->views_count) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-sm">
                                    No obituary view data logged for this timeframe yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Traffic Sources & Devices (1 Col) -->
        <div class="space-y-6">
            <!-- Traffic Sources / Referrers -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="border-b border-slate-200 pb-3">
                    <h3 class="font-serif text-lg font-bold text-slate-900">Traffic Referrers</h3>
                    <p class="text-xs text-slate-500">Where website visitors came from.</p>
                </div>

                <div class="space-y-3">
                    @forelse($referrers as $ref)
                        @php
                            $pct = $totalPageViews > 0 ? round(($ref->count / $totalPageViews) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-semibold text-slate-800">
                                <span>{{ $ref->referer_host }}</span>
                                <span class="text-slate-500 font-mono">{{ number_format($ref->count) }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-600 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No referrer data recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Device Type Breakdown -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="border-b border-slate-200 pb-3">
                    <h3 class="font-serif text-lg font-bold text-slate-900">Device Breakdown</h3>
                    <p class="text-xs text-slate-500">Visitor hardware & screen types.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center text-xs">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="material-symbols-outlined text-[20px] text-slate-600 block mb-1">smartphone</span>
                        <span class="font-bold text-slate-900 text-sm block">{{ number_format($deviceBreakdown['mobile'] ?? 0) }}</span>
                        <span class="text-slate-500 uppercase text-[10px]">Mobile</span>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="material-symbols-outlined text-[20px] text-slate-600 block mb-1">desktop_windows</span>
                        <span class="font-bold text-slate-900 text-sm block">{{ number_format($deviceBreakdown['desktop'] ?? 0) }}</span>
                        <span class="text-slate-500 uppercase text-[10px]">Desktop</span>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="material-symbols-outlined text-[20px] text-slate-600 block mb-1">tablet</span>
                        <span class="font-bold text-slate-900 text-sm block">{{ number_format($deviceBreakdown['tablet'] ?? 0) }}</span>
                        <span class="text-slate-500 uppercase text-[10px]">Tablet</span>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="material-symbols-outlined text-[20px] text-slate-600 block mb-1">smart_toy</span>
                        <span class="font-bold text-slate-900 text-sm block">{{ number_format($deviceBreakdown['bot'] ?? 0) }}</span>
                        <span class="text-slate-500 uppercase text-[10px]">Search Bots</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Traffic Trend Breakdown Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-serif text-lg font-bold text-slate-900">Daily Visitor Trends (Last 14 Days)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Chronological breakdown of pageviews and unique IP visitors.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Pageviews</th>
                        <th class="px-6 py-3.5">Unique Visitors</th>
                        <th class="px-6 py-3.5">Avg Pageviews / Visitor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($dailyTrends as $row)
                        @php
                            $avg = $row->unique_visitors > 0 ? round($row->total_views / $row->unique_visitors, 2) : 1;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                {{ Carbon\Carbon::parse($row->date)->format('M d, Y (D)') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-amber-800">
                                {{ number_format($row->total_views) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-sky-800">
                                {{ number_format($row->unique_visitors) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                {{ $avg }} views/user
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Daily traffic tracking started today. Check back as visitors view obituaries.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
