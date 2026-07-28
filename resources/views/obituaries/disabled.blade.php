@extends('layouts.app')

@section('title', 'Public Submissions Paused | Obituaries.co.ke')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-center space-y-6">
    <div class="w-20 h-20 mx-auto rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 shadow-sm">
        <span class="material-symbols-outlined text-[40px]">pause_circle</span>
    </div>

    <div class="space-y-3">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-primary">Public Submissions Paused</h1>
        <p class="text-on-surface-variant text-base sm:text-lg max-w-xl mx-auto leading-relaxed">
            Public obituary notice submissions are currently temporarily paused by administration for scheduled system maintenance or editorial review.
        </p>
    </div>

    <div class="p-6 bg-surface-container-low border border-surface-container-high rounded-2xl max-w-md mx-auto space-y-3 text-sm text-on-surface">
        <p class="font-semibold text-primary">Need Assistance or Urgent Publishing?</p>
        <p class="text-xs text-on-surface-variant">Please contact our official editorial desk directly to post an announcement on your behalf.</p>
        <div class="pt-2 text-xs font-semibold text-secondary space-y-1">
            <div>📞 Phone: {{ \App\Models\Setting::get('footer_phone', '+254 700 000 000') }}</div>
            <div>✉️ Email: {{ \App\Models\Setting::get('footer_email', 'support@obituaries.co.ke') }}</div>
        </div>
    </div>

    <div class="pt-4">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 px-8 py-3.5 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-[18px]">home</span>
            <span>Return to Homepage</span>
        </a>
    </div>
</div>
@endsection
