@extends('layouts.admin')

@section('title', 'Manage Ad Pricing Matrix')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-white">Ad Pricing Matrix</h1>
            <p class="text-xs text-slate-400">Manage daily rates, national coverage multipliers, and featured surcharges per placement & banner size.</p>
        </div>
    </div>

    <!-- Pricing Matrix Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400">Current Placement Pricing Tiers</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Placement Slot</th>
                        <th class="py-3 px-4">Banner Size</th>
                        <th class="py-3 px-4">Single County Daily Rate</th>
                        <th class="py-3 px-4">National Daily Rate</th>
                        <th class="py-3 px-4">Featured Daily Surcharge</th>
                        <th class="py-3 px-4 text-right">Update Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($pricings as $pr)
                        <tr class="hover:bg-slate-800/50">
                            <td class="py-3 px-4 font-bold text-white">
                                {{ $pr->placement->name }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-300">
                                {{ $pr->bannerSize->name }} ({{ $pr->bannerSize->dimensions }})
                            </td>
                            <form action="{{ route('admin.advertising.pricing.update', $pr->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td class="py-3 px-4 font-mono">
                                    <input type="number" step="100" name="daily_rate" value="{{ (int)$pr->daily_rate }}" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 rounded text-xs text-emerald-400 font-bold">
                                </td>
                                <td class="py-3 px-4 font-mono">
                                    <input type="number" step="100" name="national_daily_rate" value="{{ (int)$pr->national_daily_rate }}" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 rounded text-xs text-sky-400 font-bold">
                                </td>
                                <td class="py-3 px-4 font-mono">
                                    <input type="number" step="50" name="featured_sur_charge" value="{{ (int)$pr->featured_sur_charge }}" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 rounded text-xs text-amber-400 font-bold">
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button type="submit" class="px-3 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded text-xs">
                                        Save Rate
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
