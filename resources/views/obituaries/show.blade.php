@extends('layouts.app')

@section('title', $obituary->full_name . ' Obituary | Obituaries.co.ke')
@section('meta_description', 'Read the obituary, life story, funeral details and memories of ' . $obituary->full_name . '.')

@section('og_title', $obituary->full_name . ' Obituary | Obituaries.co.ke')
@section('og_description', 'Read the obituary, life story, funeral details and memories of ' . $obituary->full_name . '.')
@section('og_image', $obituary->photo ? asset('storage/' . $obituary->photo) : asset('images/og-default.jpg'))

@section('content')

<!-- Immersive Hero Section from Stitch Design -->
<section class="relative w-full min-h-[300px] sm:min-h-[420px] lg:h-[500px] flex items-end overflow-hidden border-b border-surface-container-high pb-6 sm:pb-8">
    <!-- Cover Image with Gradient Scrim -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBMukGU84gf4tb_RHChMQDd_B23JPy5Bu7V7T7TGKz5opRwHBvTpsGdK-UHzBunI2Fpmqfz80T4SmsEkU-ZlmHQMTw32EbDPwfuznH0PjAgOu1GJy548fELHnHza2bIvWbvZVoS--L_nXm_DHosxPzTXn44Zhu6PvDMZz8R5vITf88A6lz0tzxb12ZVmauEXaWiRtxNdpFUcTAR5uAWvPbTkc1GhRXAcfJ54MPWU0bwSPX95qqv_Pj3NI8Jd-wUPoiks-aTR4zOGNk-')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent"></div>
    </div>

    <!-- Profile & Vital Info Overlap -->
    <div class="relative z-10 w-full max-w-[1200px] mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center md:items-start gap-4 sm:gap-8">
        <div class="relative -mb-10 md:mb-0 flex-shrink-0">
            <div class="w-36 h-36 sm:w-44 sm:h-44 md:w-52 md:h-52 rounded-full border-4 sm:border-8 border-background overflow-hidden shadow-2xl bg-surface-container">
                @if($obituary->photo)
                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-primary to-primary-container flex flex-col items-center justify-center p-4 text-center text-on-primary">
                        <span class="material-symbols-outlined text-[36px] sm:text-[48px] text-secondary-fixed mb-1">person</span>
                        <span class="font-serif text-[10px] sm:text-xs italic">In Loving Memory</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col items-center md:items-start text-center md:text-left pt-2 sm:pt-4">
            <span class="text-[10px] sm:text-xs font-bold text-secondary tracking-[0.2em] uppercase mb-1 sm:mb-2">In Loving Memory</span>
            <h1 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-bold text-primary mb-2">{{ $obituary->full_name }}</h1>
            <div class="flex items-center gap-2 sm:gap-3 text-on-surface-variant mb-2 font-semibold text-xs sm:text-sm">
                <span>{{ $obituary->date_of_birth->format('Y') }}</span>
                <span class="w-4 sm:w-6 h-[1px] bg-outline-variant"></span>
                <span>{{ $obituary->date_of_death->format('Y') }}</span>
                @if($obituary->age)
                    <span class="text-xs text-on-surface-variant/70 font-normal">({{ $obituary->age }} Years)</span>
                @endif
            </div>
            <p class="font-serif italic text-on-surface-variant opacity-80 text-xs sm:text-sm">"Forever in our hearts"</p>
        </div>
    </div>
</section>

<!-- Content Grid from Stitch Design -->
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 mt-10 sm:mt-16 pb-16 sm:pb-20">
    
    <!-- Left Column: Biography & Life Story -->
    <div class="lg:col-span-8 space-y-8 sm:space-y-12">
        <section class="relative">
            <h2 class="font-serif text-xl sm:text-3xl font-bold text-primary mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
                <span>The Life & Journey</span>
                <span class="flex-1 h-[1px] bg-surface-container-high"></span>
            </h2>

            <div class="prose max-w-none text-on-surface text-sm sm:text-lg leading-relaxed font-serif">
                {!! nl2br(e($obituary->biography)) !!}
            </div>
        </section>

        <!-- Family Submitter Note -->
        <div class="p-4 sm:p-6 bg-surface-container-low rounded-xl border-l-4 border-secondary text-xs text-on-surface-variant flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-bold text-primary block text-xs sm:text-sm">Submitted with love by {{ $obituary->submitter_name }}</span>
                <span>Relationship: {{ $obituary->relationship }}</span>
            </div>
            <span class="font-serif italic text-secondary font-bold text-xs">Obituaries.co.ke</span>
        </div>

        <div class="pt-2">
            <a href="{{ route('obituaries.search') }}" class="text-xs font-bold text-primary hover:text-secondary inline-flex items-center space-x-1">
                <span>&larr; Return to Obituary Directory</span>
            </a>
        </div>
    </div>

    <!-- Right Column: Service Details & Actions -->
    <div class="lg:col-span-4 space-y-6 sm:space-y-8" x-data="{ copied: false }">
        
        <!-- Funeral Service Info Card -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-md p-5 sm:p-8 border border-surface-container space-y-5 sm:space-y-6">
            <h3 class="font-serif text-lg sm:text-xl font-bold text-primary border-b border-surface-container pb-3 sm:pb-4">Service Details</h3>
            
            <div class="space-y-4 text-xs">
                <!-- Location -->
                <div class="flex gap-3 sm:gap-4 items-start">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">location_on</span>
                    </div>
                    <div>
                        <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Location</p>
                        <p class="font-semibold text-on-surface text-xs sm:text-sm">{{ $obituary->town }}, {{ $obituary->county }} County</p>
                    </div>
                </div>

                @if($obituary->funeral_date)
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">calendar_today</span>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Funeral / Service Date</p>
                            <p class="font-semibold text-on-surface text-xs sm:text-sm">{{ $obituary->funeral_date->format('l, F d, Y') }}</p>
                        </div>
                    </div>
                @endif

                @if($obituary->church_service_location)
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">church</span>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Service Venue</p>
                            <p class="font-semibold text-on-surface text-xs sm:text-sm">{{ $obituary->church_service_location }}</p>
                        </div>
                    </div>
                @endif

                @if($obituary->burial_location)
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-container/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">nature_people</span>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-wider text-on-surface-variant text-[10px]">Burial Grounds</p>
                            <p class="font-semibold text-on-surface text-xs sm:text-sm">{{ $obituary->burial_location }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- PDF Programme Download Button -->
            @if($obituary->programme_file)
                <a href="{{ asset('storage/' . $obituary->programme_file) }}" target="_blank" class="w-full mt-4 flex items-center justify-center gap-2 bg-primary text-on-primary py-3.5 rounded-xl text-xs font-semibold hover:bg-primary-container transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Download Funeral Programme (PDF)</span>
                </a>
            @endif

            <!-- Share Buttons -->
            <div class="pt-5 border-t border-surface-container">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-3 text-center">Share this Memorial</p>
                
                @php
                    $shareUrl = urlencode(url()->current());
                    $shareText = urlencode("In loving memory of {$obituary->full_name}. Read full obituary and funeral details here: " . url()->current());
                @endphp

                <div class="flex justify-center gap-3">
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all" title="Share on WhatsApp">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.438 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.63 1.438h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all" title="Share on Facebook">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    <button type="button" @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 3000)" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all" title="Copy Link">
                        <span class="material-symbols-outlined text-[18px]" x-text="copied ? 'check' : 'link'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
