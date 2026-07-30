@extends('layouts.app')

@section('title', 'Complete Payment | Obituaries.co.ke')

@section('content')
@php
    $pricing = \App\Models\Setting::getPricingDetails();
    $cost = $pricing['final_price'];
@endphp

<div class="bg-slate-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-semibold uppercase tracking-widest text-amber-400 block mb-2">Step 4 of 4</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-3">M-Pesa Payment Checkout</h1>
        <p class="text-slate-300 text-sm sm:text-base max-w-xl mx-auto">
            Pay KES {{ number_format($cost) }} securely via Safaricom M-Pesa to submit your obituary for admin verification.
        </p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" 
     x-data="{ 
         statusUrl: '{{ route('payments.status', $obituary->id) }}', 
         redirectUrl: '{{ route('payments.success', $obituary->id) }}',
         statusText: 'Awaiting Payment Confirmation',
         isFailed: false,
         init() {
             setInterval(() => {
                 fetch(this.statusUrl)
                     .then(res => res.json())
                     .then(data => {
                         if (data.is_completed) {
                             this.statusText = 'Payment Received! Redirecting...';
                             window.location.href = this.redirectUrl;
                         } else if (data.status === 'failed') {
                             this.isFailed = true;
                             this.statusText = 'Payment prompt was cancelled or timed out. Please try sending prompt again.';
                         }
                     })
                     .catch(err => console.error(err));
             }, 3000);
         } 
     }">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <!-- Order Summary Top Banner -->
        <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Obituary Notice For</span>
                <h2 class="font-serif text-2xl font-bold text-slate-900 mt-1">{{ $obituary->full_name }}</h2>
                <div class="text-xs text-slate-500 mt-1 flex items-center space-x-2">
                    <span>Submitted by: <strong>{{ $obituary->submitter_name }}</strong></span>
                    <span>&bull;</span>
                    <span>{{ $obituary->town }}, {{ $obituary->county }}</span>
                </div>
            </div>
            <div class="bg-white px-5 py-3 rounded-xl border border-slate-200 shadow-sm text-right min-w-[180px]">
                @if($pricing['has_offer'])
                    <span class="text-[10px] text-slate-400 block line-through font-medium">Original KES {{ number_format($pricing['base_price']) }}</span>
                    <div class="flex items-center space-x-1.5 justify-end">
                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-bold uppercase">{{ $pricing['discount_percent'] }}% OFF</span>
                        <span class="font-serif text-2xl font-extrabold text-amber-600">KES {{ number_format($cost) }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-500 block uppercase font-medium">Total Fee</span>
                    <span class="font-serif text-2xl font-bold text-slate-900">KES {{ number_format($cost) }}</span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-10 space-y-8">
            <!-- Payment Instructions Box -->
            <div class="flex items-start space-x-4 p-5 bg-amber-50 rounded-xl border border-amber-200">
                <div class="w-10 h-10 rounded-full bg-amber-500/20 text-amber-700 flex items-center justify-center flex-shrink-0 font-bold">
                    M
                </div>
                <div class="text-xs text-amber-950 space-y-1 leading-relaxed">
                    <p class="font-bold text-sm text-amber-900">How M-Pesa STK Push Payment Works:</p>
                    <ol class="list-decimal pl-4 space-y-1">
                        <li>Enter your Safaricom M-Pesa phone number below.</li>
                        <li>Click <strong>"Send STK Push Prompt"</strong>.</li>
                        <li>Check your mobile phone for an automatic pop-up prompt.</li>
                        <li>Enter your M-Pesa PIN to complete payment.</li>
                    </ol>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('payments.stkpush', $obituary->id) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Safaricom Phone Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $obituary->submitter_phone) }}" required placeholder="e.g. 0712345678 or 254712345678" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <div class="absolute left-3 top-3.5 text-slate-400 font-bold text-sm">
                            🇰🇪
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">Accepts 07XX..., 01XX..., or 2547XX...</span>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-base transition-all shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Send STK Push Prompt (KES {{ number_format($cost) }})</span>
                    </button>
                </div>
            </form>

            <!-- Status Indicator -->
            <div class="p-6 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-3">
                <div class="flex items-center justify-center space-x-2">
                    <div class="w-3 h-3 rounded-full" :class="isFailed ? 'bg-rose-500' : 'bg-amber-500 animate-ping'"></div>
                    <span class="text-xs font-semibold uppercase tracking-wider" :class="isFailed ? 'text-rose-700 font-bold' : 'text-slate-600'" x-text="statusText"></span>
                </div>
                <p class="text-xs text-slate-500" x-show="!isFailed">
                    Once you enter your PIN on your Safaricom phone, this page will automatically reconcile and redirect to your receipt.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
