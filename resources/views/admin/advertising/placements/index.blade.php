@extends('layouts.admin')

@section('title', 'Manage Ad Placements')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Ad Placement Slots</h1>
            <p class="text-xs text-slate-400">Database-driven ad slots across Homepage, Obituary Detail, Search, Category, and County pages.</p>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Configured Banner Placements</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Placement Name</th>
                        <th class="py-3 px-4">Slug Code</th>
                        <th class="py-3 px-4">Page Type</th>
                        <th class="py-3 px-4">Supported Dimensions</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($placements as $pl)
                        <tr class="hover:bg-slate-800/50">
                            <td class="py-3 px-4 font-bold text-white">
                                {{ $pl->name }}
                            </td>
                            <td class="py-3 px-4 font-mono text-amber-400">
                                <code>{{ $pl->slug }}</code>
                            </td>
                            <td class="py-3 px-4 text-slate-300 uppercase font-semibold">
                                {{ $pl->page_type }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $pl->bannerSizes->pluck('dimensions')->join(', ') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-950 text-emerald-300 border border-emerald-800">Active</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
