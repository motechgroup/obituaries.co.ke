@extends('layouts.app')

@section('title', "{$county} Obituaries & Death Notices | Obituaries.co.ke")
@section('meta_description', "Browse recent obituaries, death notices, funeral service announcements, and virtual tributes from {$county} County, Kenya. Honor memories and send condolences.")
@section('seo_keywords', "{$county} obituaries, {$county} death notices, {$county} funeral announcements, obituaries in {$county} Kenya")

@section('content')
<div class="bg-surface-container-low py-10 sm:py-14 border-b border-outline-variant/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-bold text-on-surface-variant mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ route('obituaries.search') }}" class="hover:text-primary transition-colors">Obituaries Directory</a>
            <span>&rsaquo;</span>
            <span class="text-primary">{{ $county }} County</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider mb-3 border border-amber-300/30">
                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                    <span>{{ $county }} County Registry</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-primary tracking-tight">
                    {{ $county }} Obituaries & Death Notices
                </h1>
                <p class="mt-3 text-sm sm:text-base text-on-surface-variant max-w-3xl leading-relaxed">
                    Explore recent obituary notices, funeral service schedules, and memorial tributes for loved ones from {{ $county }} County, Kenya. Share condolences and light virtual candles in remembrance.
                </p>
            </div>

            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 shadow-2xs flex-shrink-0 text-center sm:text-right">
                <span class="text-xs uppercase tracking-wider font-bold text-on-surface-variant block mb-0.5">Published Records</span>
                <span class="font-serif text-2xl sm:text-3xl font-bold text-primary">{{ number_format($totalCount) }}</span>
                <span class="text-xs text-on-surface-variant/80 block">Notices in {{ $county }}</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($obituaries->isEmpty())
        <div class="bg-surface-container-lowest rounded-2xl p-12 text-center border border-dashed border-outline-variant max-w-xl mx-auto my-8">
            <span class="material-symbols-outlined text-4xl text-on-surface-variant/50 mb-3">church</span>
            <h3 class="font-serif text-xl font-bold text-primary mb-2">No Notices Published in {{ $county }} Yet</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-6">
                Be the first to publish an official obituary notice to honor your loved one in {{ $county }} County and notify family and friends worldwide.
            </p>
            <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Submit Obituary Notice</span>
            </a>
        </div>
    @else
        <!-- Obituary Directory Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($obituaries as $obituary)
                <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/20 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden cursor-pointer hover:-translate-y-1">
                    <div>
                        <div class="relative aspect-4/3 bg-surface-container overflow-hidden select-none">
                            @if($obituary->photo)
                                <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }} obituary photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 select-none pointer-events-none">
                                
                                <!-- Translucent Glass Watermark Overlay (Chest Level) -->
                                <div class="absolute inset-x-0 text-center pointer-events-none z-10 select-none" style="bottom: 14%;">
                                    <span class="font-serif font-black text-[9px] sm:text-[11px] tracking-[0.16em] uppercase select-none pointer-events-none" style="color: rgba(255, 255, 255, 0.55); -webkit-text-fill-color: rgba(255, 255, 255, 0.55); text-shadow: 0 1px 3px rgba(255, 255, 255, 0.9), 0 -1px 2px rgba(0, 0, 0, 0.7), 0 0 8px rgba(255, 255, 255, 0.8); filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.7)); transform: rotate(-5deg); display: inline-block;">
                                        Obituaries<span style="color: rgba(254, 243, 199, 0.7); -webkit-text-fill-color: rgba(254, 243, 199, 0.7);">.co.ke</span>
                                    </span>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-amber-900 to-amber-950 flex flex-col items-center justify-center p-4 text-center text-amber-100">
                                    <span class="material-symbols-outlined text-3xl mb-1 text-amber-300">church</span>
                                    <span class="font-serif text-xs italic">In Loving Memory</span>
                                </div>
                            @endif

                            <div class="absolute top-3 right-3 bg-surface-container-lowest/90 backdrop-blur-xs text-primary text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-outline-variant/30 flex items-center space-x-1">
                                <span class="material-symbols-outlined text-[12px] text-amber-600">verified</span>
                                <span>Verified</span>
                            </div>
                        </div>

                        <div class="p-5">
                            <span class="text-[10px] font-bold tracking-widest text-amber-800 uppercase block mb-1">
                                {{ $obituary->town }}, {{ $obituary->county }}
                            </span>
                            <h2 class="font-serif text-lg font-bold text-primary group-hover:text-amber-800 transition-colors leading-tight mb-2 line-clamp-2">
                                {{ $obituary->full_name }}
                            </h2>
                            <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed mb-4">
                                {{ $obituary->biography }}
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 bg-surface-container-low border-t border-outline-variant/10 flex items-center justify-between text-[11px] text-on-surface-variant">
                        <span>Passed {{ $obituary->date_of_death->format('M d, Y') }}</span>
                        <span class="font-bold text-primary group-hover:text-amber-800 transition-colors">&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $obituaries->links() }}
        </div>
    @endif

    <!-- County Cross-Linking SEO Section -->
    <div class="mt-16 pt-12 border-t border-outline-variant/30">
        <h2 class="font-serif text-2xl font-bold text-primary mb-2">Browse Obituaries by County in Kenya</h2>
        <p class="text-xs text-on-surface-variant mb-6">Find death announcements and obituary notices across all 47 counties in Kenya.</p>

        <div class="flex flex-wrap gap-2">
            @foreach($allCounties as $c)
                @php $cSlug = \Illuminate\Support\Str::slug($c); @endphp
                <a href="{{ url('/county/' . $cSlug . '-obituaries') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold {{ $c === $county ? 'bg-amber-600 text-white shadow-xs' : 'bg-surface-container-low hover:bg-amber-100 text-on-surface hover:text-amber-900 border border-outline-variant/30' }} transition-all">
                    {{ $c }} Obituaries
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
