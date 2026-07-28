@extends('layouts.app')

@section('title', 'Payment Received | Obituaries.co.ke')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12 text-center space-y-6">
        <!-- Success Icon -->
        <div class="w-20 h-20 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 block mb-1">Payment Successful</span>
            @if($obituary->status === 'published')
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-slate-900 mb-3">Obituary Notice Published Live!</h1>
                <p class="text-slate-600 text-base max-w-lg mx-auto">
                    Thank you. Your M-Pesa payment of <strong>KES {{ number_format($payment->amount ?? 500, 2) }}</strong> was received successfully and your obituary notice is now active live on the platform.
                </p>
            @else
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-slate-900 mb-3">Obituary Submitted for Verification</h1>
                <p class="text-slate-600 text-base max-w-lg mx-auto">
                    Thank you. Your M-Pesa payment of <strong>KES {{ number_format($payment->amount ?? 500, 2) }}</strong> was received successfully.
                </p>
            @endif
        </div>

        <!-- Receipt Box -->
        @if($payment)
            <div class="p-6 bg-slate-50 rounded-xl border border-slate-200 max-w-md mx-auto text-left text-xs space-y-2">
                <div class="flex justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500">M-Pesa Receipt Number:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $payment->mpesa_receipt_number ?? 'Pending' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500">Amount Paid:</span>
                    <span class="font-semibold text-slate-900">KES {{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500">Deceased Name:</span>
                    <span class="font-semibold text-slate-900">{{ $obituary->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status:</span>
                    @if($obituary->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                            Published Live
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 uppercase">
                            Pending Verification
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Next Steps Info Box -->
        @if($obituary->status === 'published')
            <div class="p-6 bg-emerald-50 rounded-xl border border-emerald-200 text-left max-w-lg mx-auto space-y-2">
                <h3 class="font-serif font-bold text-emerald-950 text-base flex items-center space-x-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <span>Your Notice is Live Online</span>
                </h3>
                <p class="text-xs text-emerald-900 leading-relaxed">
                    The obituary notice for <strong>{{ $obituary->full_name }}</strong> is live on <strong>Obituaries.co.ke</strong>. You can view the announcement, share it with family and friends, or print the memorial tribute.
                </p>
            </div>
        @else
            <div class="p-6 bg-amber-50 rounded-xl border border-amber-200 text-left max-w-lg mx-auto space-y-2">
                <h3 class="font-serif font-bold text-amber-950 text-base flex items-center space-x-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>What Happens Next?</span>
                </h3>
                <p class="text-xs text-amber-900 leading-relaxed">
                    Our editorial team will review your submission and contact you at <strong>{{ $obituary->submitter_phone }}</strong> to confirm details. Once verified, the obituary will be published live on <strong>Obituaries.co.ke</strong>.
                </p>
            </div>
        @endif

        <!-- Action Links -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
            @if($obituary->status === 'published')
                <a href="{{ route('obituaries.show', $obituary->slug) }}" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-md flex items-center justify-center space-x-2">
                    <span>View Published Obituary Notice</span>
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </a>
            @endif
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-colors">
                Return to Homepage
            </a>
            <a href="{{ route('obituaries.submit') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-xl text-sm transition-colors">
                Submit Another Obituary
            </a>
        </div>
    </div>
</div>
@endsection
