@extends('layouts.admin')

@section('title', 'Platform Settings | Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="font-serif text-3xl font-bold text-slate-900">Platform Settings</h1>
        <p class="text-slate-500 text-sm mt-1">Configure site-wide obituary publishing pricing and settings.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-bold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="obituary_publishing_cost" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                    Obituary Publishing Fee (KES)
                </label>
                <div class="relative max-w-xs">
                    <span class="absolute left-4 top-3 text-slate-400 font-bold text-sm">KES</span>
                    <input type="number" step="0.01" min="0" name="obituary_publishing_cost" id="obituary_publishing_cost" value="{{ old('obituary_publishing_cost', $publishingCost) }}" required class="w-full pl-16 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-base font-bold text-slate-900 focus:ring-2 focus:ring-amber-500">
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    This fee will be dynamically displayed to submitters on the submission form, pricing badges, and M-Pesa STK push checkout!
                </p>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                    Save Pricing Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
