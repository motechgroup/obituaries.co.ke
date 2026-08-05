@extends('layouts.admin')

@section('title', 'Manage Ad Pricing Matrix | Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Ad Pricing Matrix</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Manage daily rates, national coverage multipliers, and featured surcharges per placement & banner size.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Pricing Matrix Table -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 border-b border-slate-100 pb-2">Current Placement Pricing Tiers</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Placement Slot</th>
                        <th class="py-3.5 px-4">Banner Size</th>
                        <th class="py-3.5 px-4">Single County Daily Rate</th>
                        <th class="py-3.5 px-4">National Daily Rate</th>
                        <th class="py-3.5 px-4">Featured Daily Surcharge</th>
                        <th class="py-3.5 px-4 text-right">Update Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($pricings as $pr)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $pr->placement->name }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-700 font-semibold">
                                {{ $pr->bannerSize->name }} ({{ $pr->bannerSize->dimensions }})
                            </td>
                            <form action="{{ route('admin.advertising.pricing.update', $pr->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td class="py-3.5 px-4 font-mono">
                                    <input type="number" step="100" name="daily_rate" value="{{ (int)$pr->daily_rate }}" class="w-28 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-emerald-800 font-extrabold focus:bg-white focus:border-emerald-600 outline-none">
                                </td>
                                <td class="py-3.5 px-4 font-mono">
                                    <input type="number" step="100" name="national_daily_rate" value="{{ (int)$pr->national_daily_rate }}" class="w-28 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-sky-800 font-extrabold focus:bg-white focus:border-sky-600 outline-none">
                                </td>
                                <td class="py-3.5 px-4 font-mono">
                                    <input type="number" step="50" name="featured_sur_charge" value="{{ (int)$pr->featured_sur_charge }}" class="w-28 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-amber-900 font-extrabold focus:bg-white focus:border-amber-600 outline-none">
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition-all shadow-sm">
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
