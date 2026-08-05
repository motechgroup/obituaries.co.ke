@extends('layouts.admin')

@section('title', 'Manage Ad Placements | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Ad Placement Slots</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Database-driven ad slots across Homepage, Obituary Detail, Search, Category, and County pages.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Configured Banner Placements</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Placement Name</th>
                        <th class="py-3.5 px-4">Slug Code</th>
                        <th class="py-3.5 px-4">Page Type</th>
                        <th class="py-3.5 px-4">Supported Dimensions</th>
                        <th class="py-3.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($placements as $pl)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $pl->name }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-amber-900 font-bold">
                                <code>{{ $pl->slug }}</code>
                            </td>
                            <td class="py-3.5 px-4 text-slate-800 uppercase font-extrabold">
                                {{ $pl->page_type }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                {{ $pl->bannerSizes->pluck('dimensions')->join(', ') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">Active</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
