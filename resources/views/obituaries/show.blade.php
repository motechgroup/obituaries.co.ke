@extends('layouts.app')

@section('title', $obituary->full_name . ' Obituary | Obituaries.co.ke')
@section('meta_description', 'Read the obituary, life story, funeral details and memories of ' . $obituary->full_name . '.')

@section('og_title', $obituary->full_name . ' Obituary | Obituaries.co.ke')
@section('og_description', 'Read the obituary, life story, funeral details and memories of ' . $obituary->full_name . '.')
@section('og_image', $obituary->photo ? asset('storage/' . $obituary->photo) : asset('images/og-default.jpg'))

@section('content')
<!-- Editorial Hero Header -->
<div class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white relative py-16 sm:py-20 border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold mb-6">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Verified Official Obituary Notice</span>
        </div>

        <h1 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-white mb-4 leading-tight">
            {{ $obituary->full_name }}
        </h1>

        <!-- Dates Banner -->
        <div class="flex items-center justify-center space-x-3 text-sm sm:text-base font-semibold text-amber-400 uppercase tracking-wider mb-2">
            <span>{{ $obituary->date_of_birth->format('F d, Y') }}</span>
            <span class="text-slate-600">&bull;</span>
            <span>{{ $obituary->date_of_death->format('F d, Y') }}</span>
        </div>

        @if($obituary->age)
            <div class="text-xs text-slate-400 font-medium">
                Lived {{ $obituary->age }} Full Years
            </div>
        @endif
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        
        <!-- Left Column: Photo & Details -->
        <div class="md:col-span-4 space-y-6">
            <!-- Profile Photo -->
            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-md">
                <div class="relative aspect-3/4 rounded-xl bg-slate-900 overflow-hidden">
                    @if($obituary->photo)
                        <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-b from-slate-800 to-slate-950 flex flex-col items-center justify-center p-6 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="font-serif text-sm italic">In Cherished Memory</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Key Info Box -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 text-xs">
                <h3 class="font-serif text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Location & Service</h3>

                <div>
                    <span class="text-slate-400 font-semibold uppercase block">Location</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $obituary->town }}, {{ $obituary->county }} County</span>
                </div>

                @if($obituary->funeral_date)
                    <div>
                        <span class="text-slate-400 font-semibold uppercase block">Funeral / Service Date</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $obituary->funeral_date->format('l, F d, Y') }}</span>
                    </div>
                @endif

                @if($obituary->church_service_location)
                    <div>
                        <span class="text-slate-400 font-semibold uppercase block">Service Venue</span>
                        <span class="font-semibold text-slate-800">{{ $obituary->church_service_location }}</span>
                    </div>
                @endif

                @if($obituary->burial_location)
                    <div>
                        <span class="text-slate-400 font-semibold uppercase block">Burial Location</span>
                        <span class="font-semibold text-slate-800">{{ $obituary->burial_location }}</span>
                    </div>
                @endif

                <!-- PDF Programme Download Button -->
                @if($obituary->programme_file)
                    <div class="pt-2 border-t border-slate-100">
                        <a href="{{ asset('storage/' . $obituary->programme_file) }}" target="_blank" class="w-full py-3 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition-colors flex items-center justify-center space-x-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Download Funeral Programme (PDF)</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Social Sharing Widgets -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4" x-data="{ copied: false }">
                <h3 class="font-serif text-base font-bold text-slate-900">Share Tribute</h3>

                @php
                    $shareUrl = urlencode(url()->current());
                    $shareText = urlencode("In loving memory of {$obituary->full_name}. Read full obituary, funeral details and memories here: " . url()->current());
                @endphp

                <div class="space-y-2">
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.14 4.162 4.183-1.095z"/>
                        </svg>
                        <span>Share on WhatsApp</span>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-600 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Share on Facebook</span>
                    </a>

                    <!-- Copy Link -->
                    <button type="button" @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 3000)" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition-colors flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="copied ? 'Link Copied!' : 'Copy Direct Link'"></span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Column: Biography & Life Tribute -->
        <div class="md:col-span-8 space-y-8">
            
            <!-- Bio Card -->
            <div class="bg-white rounded-2xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-6">
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 border-b border-slate-100 pb-4">
                    Life Story & Announcement
                </h2>

                <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-base sm:text-lg font-serif">
                    {!! nl2br(e($obituary->biography)) !!}
                </div>
            </div>

            <!-- Submitter Appreciation -->
            <div class="p-6 bg-amber-50/70 rounded-2xl border border-amber-200/80 text-xs text-amber-950 flex items-center justify-between">
                <div>
                    <span class="font-bold text-amber-900 block text-sm">Submitted with Love by {{ $obituary->submitter_name }}</span>
                    <span class="text-amber-800">Relationship: {{ $obituary->relationship }}</span>
                </div>
                <div class="text-amber-700 italic font-serif">
                    Obituaries.co.ke
                </div>
            </div>

            <!-- Back to Search Link -->
            <div class="pt-4 flex justify-start">
                <a href="{{ route('obituaries.search') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-800 flex items-center space-x-1.5">
                    <span>&larr; Return to Obituary Directory</span>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
