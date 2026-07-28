@extends('layouts.app')

@section('title', 'Obituaries.co.ke | Remembering Lives. Sharing Memories.')

@section('content')

<!-- Immersive Hero Section from Stitch Design -->
<section class="relative w-full min-h-[480px] sm:min-h-[580px] lg:h-[650px] flex items-center overflow-hidden py-12 lg:py-0">
    <!-- Cover Image with Gradient Scrim -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center animate-ken-burns" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAX9PfmGNd6COO34ia8jh6uRhqSKwIofOR8rZW8cgAnNwMH3tP1UF3CZoFM6J5iZJE5_RiTeLT8EdFGNX5813RF5aH8e_9Qh-j_Aa6PlHJ_CU5GhXpiWL3iIkjCNBE5tS_CAh8IUke7mq1LARZhVFHiHiONDoNvPG6WlbGSaF6F0dUARP99swDYvlmJF_GBO_MKQTXW0S4AD0qBEADEnppuKEEfg4E3WSxqHE-jdby2Xue9MuZu9BC8LimsaHd81IVXuLEaP8y6jFfw')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-surface via-surface/90 to-surface/40 sm:to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-[1200px] mx-auto px-4 sm:px-6 w-full">
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-4 sm:mb-6">
                <div class="h-[1px] w-8 sm:w-12 bg-secondary"></div>
                <span class="text-[10px] sm:text-xs font-semibold text-secondary tracking-widest uppercase">Honoring Every Journey</span>
            </div>
            
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold text-primary mb-4 sm:mb-6 leading-tight">
                Remembering Lives.<br/>
                <span class="italic font-normal">Sharing Memories.</span>
            </h1>

            <p class="text-sm sm:text-lg text-on-surface-variant mb-8 sm:mb-10 max-w-lg leading-relaxed">
                A dignified space to create and preserve a lasting digital sanctuary for your loved ones. We help you tell their story with the grace it deserves.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl text-xs sm:text-sm font-semibold hover:shadow-xl transition-all duration-300 flex items-center justify-center space-x-2 w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Submit Obituary</span>
                </a>
                <a href="#search-archives" class="border border-outline text-primary px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl text-xs sm:text-sm font-semibold hover:bg-surface-container transition-all duration-300 flex items-center justify-center space-x-2 w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Search Archives</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Dark Obituaries Directory Section (Posted Today / Latest Notices) -->
<section class="w-full bg-[#0B101D] border-y border-slate-800/80 py-5 sm:py-6 overflow-hidden">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
        <!-- Directory Header Badge -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800/60">
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider border border-amber-500/20 flex items-center space-x-1.5">
                    <span class="material-symbols-outlined text-[14px]">church</span>
                    <span>LATEST NOTICES</span>
                </span>
            </div>
            <a href="{{ route('obituaries.search') }}" class="text-[11px] sm:text-xs font-bold text-slate-400 hover:text-amber-400 transition-colors flex items-center space-x-1">
                <span>Browse All Archives</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 py-1">
            @forelse($todayNotices as $obituary)
                <a href="{{ route('obituaries.show', $obituary->slug) }}" class="flex items-center space-x-3 sm:space-x-3.5 group transition-opacity hover:opacity-90 min-w-0">
                    <!-- Thumbnail Avatar -->
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl overflow-hidden bg-slate-800 flex-shrink-0 border border-slate-700/60 shadow-xs">
                        @if($obituary->photo)
                            <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-amber-400">
                                <span class="material-symbols-outlined text-[18px] sm:text-[20px]">church</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Deceased Name & Date of Death -->
                    <div class="text-left min-w-0 flex-1">
                        <h4 class="font-bold text-white text-xs sm:text-sm group-hover:text-amber-400 transition-colors leading-tight truncate">
                            {{ $obituary->full_name }}
                        </h4>
                        <span class="text-[10px] sm:text-xs text-slate-400 block mt-0.5 font-medium truncate">
                            {{ $obituary->date_of_death->format('M d, Y') }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="text-slate-400 text-xs py-2 italic">No published notices posted today yet.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Recent Tributes Section (Notices At Least 2 Days Old) -->
<section class="w-full py-12 sm:py-20 bg-surface">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
        <div class="flex flex-row justify-between items-end mb-8 sm:mb-12">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-primary mb-1 sm:mb-2">Recent Tributes</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant">Honoring those who recently joined the ancestors.</p>
            </div>
            <a href="{{ route('obituaries.search') }}" class="group flex items-center gap-1 sm:gap-2 text-xs font-bold text-primary hover:text-secondary transition-colors">
                <span class="hidden sm:inline">View All Archives</span>
                <span class="sm:hidden">All</span>
                <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
        </div>

        @if($recentNotices->isEmpty())
            <div class="bg-surface-container-lowest rounded-2xl p-8 sm:p-12 text-center border border-outline-variant/30 max-w-xl mx-auto">
                <span class="material-symbols-outlined text-[40px] sm:text-[48px] text-on-surface-variant/40 mb-3">auto_stories</span>
                <h3 class="font-serif text-lg sm:text-xl font-bold text-primary mb-2">No Published Obituaries Yet</h3>
                <p class="text-xs text-on-surface-variant mb-6">Be the first to publish a verified tribute for your loved one.</p>
                <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center px-6 py-3 bg-primary text-on-primary rounded-xl text-xs font-semibold">
                    Submit an Obituary
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($recentNotices as $obituary)
                    <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group relative bg-surface-container-lowest p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between border border-outline-variant/20 block cursor-pointer">
                        <div>
                            <!-- Compact Square Aspect Image Container -->
                            <div class="relative aspect-square mb-3 overflow-hidden rounded-lg sm:rounded-xl bg-surface-container select-none">
                                @if($obituary->photo)
                                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 scale-105 group-hover:scale-100 select-none pointer-events-none">
                                    
                                    <!-- Translucent Glass Watermark Overlay (Chest Level) -->
                                    <div class="absolute inset-x-0 text-center pointer-events-none z-10 select-none" style="bottom: 14%;">
                                        <span class="font-serif font-black text-[8px] sm:text-[10px] tracking-[0.14em] select-none pointer-events-none" style="color: rgba(255, 255, 255, 0.65); -webkit-text-fill-color: rgba(255, 255, 255, 0.65); text-shadow: 0 1px 3px rgba(255, 255, 255, 0.9), 0 -1px 2px rgba(0, 0, 0, 0.7), 0 0 8px rgba(255, 255, 255, 0.8); filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.7)); transform: rotate(-5deg); display: inline-block;">Obituaries.co.ke</span>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-b from-primary-container to-primary flex flex-col items-center justify-center p-3 text-center text-on-primary">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center text-secondary-fixed mb-1">
                                            <span class="material-symbols-outlined text-[18px] sm:text-[22px]">church</span>
                                        </div>
                                        <span class="font-serif text-[9px] sm:text-[10px] italic">In Loving Memory</span>
                                    </div>
                                @endif

                                @if($obituary->is_anniversary_today)
                                    <div class="absolute top-2 left-2 bg-amber-700/90 backdrop-blur-md text-amber-100 text-[8px] sm:text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider border border-amber-300/30 shadow-xs z-10">
                                        <span>{{ $obituary->anniversary_badge_text }}</span>
                                    </div>
                                @endif

                                <div class="absolute top-2 right-2 z-10 select-none pointer-events-none" title="Verified Notice">
                                    <span class="material-symbols-outlined text-amber-400 text-[18px] sm:text-[20px] drop-shadow-md leading-none">verified</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <span class="text-[9px] sm:text-[10px] font-semibold text-secondary tracking-widest uppercase mb-0.5 block truncate">{{ $obituary->town }}, {{ $obituary->county }}</span>
                                <h3 class="font-serif text-xs sm:text-base font-bold text-primary mb-0.5 group-hover:text-secondary transition-colors line-clamp-2 leading-snug">{{ $obituary->full_name }}</h3>
                                <p class="text-[10px] text-on-surface-variant/70 italic">
                                    @if($obituary->date_of_birth)
                                        {{ $obituary->date_of_birth->format('Y') }} &mdash; {{ $obituary->date_of_death->format('Y') }}
                                        @if($obituary->age) ({{ $obituary->age }} Yrs) @endif
                                    @else
                                        Passed Away: {{ $obituary->date_of_death->format('M j, Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        @if($todayAnniversaries->isNotEmpty())
            <!-- Today's Anniversaries Section (List View Format for Mobile & Desktop) -->
            <div class="mt-14 sm:mt-20 pt-10 sm:pt-14 border-t border-outline-variant/30">
                <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-6 sm:mb-8 gap-3">
                    <div>
                        <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-amber-500/10 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-amber-300/30">
                            <span>🌹 In Loving Remembrance</span>
                        </div>
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-primary mb-1">Today's Anniversaries</h2>
                        <p class="text-xs sm:text-sm text-on-surface-variant">Remembering loved ones whose anniversary of passing falls on today's date.</p>
                    </div>
                    <a href="{{ route('obituaries.search', ['filter' => 'anniversaries']) }}" class="text-xs font-bold text-amber-800 hover:text-amber-900 inline-flex items-center space-x-1 whitespace-nowrap">
                        <span>View All Anniversaries</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="divide-y divide-amber-200/60 border-y border-amber-200/60">
                    @foreach($todayAnniversaries as $obituary)
                        <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group flex flex-col sm:flex-row sm:items-center justify-between py-3.5 sm:py-4 px-2 sm:px-3 rounded-xl hover:bg-amber-50/70 transition-all duration-300 gap-3">
                            <!-- Left: Avatar & Deceased Info -->
                            <div class="flex items-center space-x-3.5 sm:space-x-4 min-w-0">
                                <!-- Thumbnail Avatar -->
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl overflow-hidden bg-surface-container flex-shrink-0 border border-amber-300/50 shadow-xs">
                                    @if($obituary->photo)
                                        <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-amber-800 to-amber-950 flex items-center justify-center text-amber-100">
                                            <span class="material-symbols-outlined text-[20px] sm:text-[24px]">church</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Deceased Details -->
                                <div class="text-left min-w-0 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-[9px] sm:text-[10px] font-semibold text-amber-800 tracking-widest uppercase truncate block">{{ $obituary->town }}, {{ $obituary->county }}</span>
                                    </div>
                                    <h3 class="font-serif text-sm sm:text-base font-bold text-primary group-hover:text-amber-800 transition-colors leading-tight truncate">
                                        {{ $obituary->full_name }}
                                    </h3>
                                    <p class="text-[11px] sm:text-xs text-on-surface-variant/70 mt-0.5 truncate">
                                        Passed {{ $obituary->date_of_death->format('M d, Y') }} &bull; <span class="italic text-amber-900/80 font-medium">{{ $obituary->anniversary_badge_text }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Anniversary Badge Pill & View Link -->
                            <div class="flex items-center justify-between sm:justify-end space-x-3 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-amber-200/40">
                                <span class="bg-amber-100 text-amber-900 text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full border border-amber-300/60 shadow-2xs whitespace-nowrap">
                                    {{ $obituary->anniversary_badge_text }}
                                </span>
                                <span class="text-xs font-bold text-primary group-hover:text-amber-800 transition-colors flex items-center space-x-1 whitespace-nowrap">
                                    <span>View Tribute</span>
                                    <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Obituaries Directory Section (Random Notices Cards Grid Replaces Search) -->
<section class="w-full py-14 sm:py-20 bg-slate-950 text-white overflow-hidden relative border-t border-slate-800/80" id="obituaries-directory">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-amber-500/5 to-transparent pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px]"></div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 relative z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 sm:mb-12 gap-4 border-b border-slate-800/80 pb-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-2.5 border border-amber-500/20">
                    <span class="material-symbols-outlined text-[16px]">church</span>
                    <span>Obituaries Directory</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-white mb-2">Explore Memorial Directory</h2>
                <p class="text-xs sm:text-sm text-slate-400">Discover and honor memorial notices & tributes across Kenya.</p>
            </div>
            <a href="{{ route('obituaries.search') }}" class="group flex items-center gap-1 sm:gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors">
                <span>View Full Archives &rarr;</span>
            </a>
        </div>

        @if($randomNotices->isEmpty())
            <div class="text-center py-12 text-slate-400 text-xs italic">
                No obituaries available in directory.
            </div>
        @else
            <!-- 3 Items Per Line Grid (Equal Size Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($randomNotices as $obituary)
                    <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group relative bg-slate-900/90 p-4 rounded-2xl shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between border border-slate-800/90 hover:border-amber-500/40 cursor-pointer">
                        <div>
                            <!-- Uniform Square Aspect Image Container -->
                            <div class="relative aspect-square mb-4 overflow-hidden rounded-xl bg-slate-800 select-none">
                                @if($obituary->photo)
                                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 scale-105 group-hover:scale-100 select-none pointer-events-none">
                                    
                                    <!-- Translucent Glass Watermark Overlay -->
                                    <div class="absolute inset-x-0 text-center pointer-events-none z-10 select-none" style="bottom: 14%;">
                                        <span class="font-serif font-black text-[9px] sm:text-[11px] tracking-[0.14em] select-none pointer-events-none" style="color: rgba(255, 255, 255, 0.65); -webkit-text-fill-color: rgba(255, 255, 255, 0.65); text-shadow: 0 1px 3px rgba(255, 255, 255, 0.9), 0 -1px 2px rgba(0, 0, 0, 0.7), 0 0 8px rgba(255, 255, 255, 0.8); filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.7)); transform: rotate(-5deg); display: inline-block;">Obituaries.co.ke</span>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-b from-amber-950 to-slate-900 flex flex-col items-center justify-center p-4 text-center text-amber-100">
                                        <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-amber-300 mb-2">
                                            <span class="material-symbols-outlined text-[24px]">church</span>
                                        </div>
                                        <span class="font-serif text-xs italic">In Loving Memory</span>
                                    </div>
                                @endif

                                <div class="absolute top-2.5 right-2.5 z-10 select-none pointer-events-none" title="Verified Notice">
                                    <span class="material-symbols-outlined text-amber-400 text-[20px] drop-shadow-md leading-none">verified</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <span class="text-[10px] font-bold text-amber-400 tracking-widest uppercase mb-1 block truncate">{{ $obituary->town }}, {{ $obituary->county }}</span>
                                <h3 class="font-serif text-base sm:text-lg font-bold text-white mb-1 group-hover:text-amber-400 transition-colors line-clamp-2 leading-snug">{{ $obituary->full_name }}</h3>
                                <p class="text-xs text-slate-400 italic">
                                    @if($obituary->date_of_birth)
                                        {{ $obituary->date_of_birth->format('Y') }} &mdash; {{ $obituary->date_of_death->format('Y') }}
                                        @if($obituary->age) ({{ $obituary->age }} Yrs) @endif
                                    @else
                                        Passed Away: {{ $obituary->date_of_death->format('M j, Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Virtual Candles Lit Today Section (Matching User Directive: After Search Section) -->
<section class="w-full py-12 sm:py-16 bg-slate-950 text-white relative overflow-hidden border-b border-amber-950/40">
    <!-- Subtle Golden Glow Background Accent -->
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 relative z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-2.5 border border-amber-500/20">
                    <span class="animate-pulse text-amber-400">🕯️</span>
                    <span>Virtual Candles Lit Today</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-white">Candles Lit in Memory</h2>
                <p class="text-xs sm:text-sm text-slate-400">Tributes and prayers offered by visitors for their loved ones.</p>
            </div>
            <a href="{{ route('obituaries.search') }}" class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center space-x-1">
                <span>Light a Candle for Someone</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if($todayCandlesObituaries->isEmpty())
            <div class="text-slate-400 text-xs py-4 italic text-center">
                No virtual candles lit yet today. Be the first to light a candle on an obituary memorial.
            </div>
        @else
            <div class="divide-y divide-slate-800/80">
                @foreach($todayCandlesObituaries as $obituary)
                    <a href="{{ route('obituaries.show', $obituary->slug) }}#candles" class="group flex flex-col sm:flex-row sm:items-center justify-between py-4 sm:py-5 hover:bg-slate-900/50 px-2 rounded-xl transition-all duration-300 gap-4">
                        <!-- Left Avatar & Details -->
                        <div class="flex items-center space-x-3.5 sm:space-x-4 min-w-0">
                            <!-- Thumbnail Avatar -->
                            <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl overflow-hidden bg-slate-800 flex-shrink-0 border border-slate-700/60 shadow-xs">
                                @if($obituary->photo)
                                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-amber-950 to-slate-900 flex items-center justify-center text-amber-300">
                                        <span class="text-lg animate-pulse">🕯️</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Deceased Info & Location -->
                            <div class="text-left min-w-0">
                                <span class="text-[9px] sm:text-[10px] font-semibold text-amber-400 tracking-widest uppercase block mb-0.5">{{ $obituary->town }}, {{ $obituary->county }}</span>
                                <h3 class="font-bold text-white text-sm sm:text-base group-hover:text-amber-400 transition-colors leading-tight truncate">
                                    {{ $obituary->full_name }}
                                </h3>
                                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 truncate">
                                    Passed {{ $obituary->date_of_death->format('M d, Y') }} &bull; <span class="italic text-slate-300">In Loving Memory</span>
                                </p>
                            </div>
                        </div>

                        <!-- Right: Candle Count Badge & CTA -->
                        <div class="flex items-center justify-between sm:justify-end space-x-4 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800/50">
                            <div class="bg-amber-500/10 text-amber-300 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-500/20 flex items-center space-x-1.5 shadow-xs">
                                <span class="animate-pulse text-amber-400 text-sm">🕯️</span>
                                <span>{{ $obituary->candles_count }} {{ Str::plural('Candle', $obituary->candles_count) }} Lit</span>
                            </div>

                            <span class="text-xs font-bold text-amber-400 group-hover:text-amber-300 transition-colors flex items-center space-x-1 whitespace-nowrap">
                                <span>Light Candle</span>
                                <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Memorial Quote Section from Stitch Design (Daily Shuffling Quote) -->
<section class="w-full py-14 sm:py-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 bg-surface-container-lowest border-t border-outline-variant/10">
    <div class="w-12 h-12 sm:w-16 sm:h-16 mb-4 sm:mb-6 text-secondary/40 flex items-center justify-center">
        <span class="material-symbols-outlined text-[36px] sm:text-[48px]">format_quote</span>
    </div>
    <blockquote class="font-serif text-xl sm:text-3xl text-primary max-w-2xl mb-4 sm:mb-6 italic leading-relaxed">
        "{{ $dailyQuote['text'] }}"
    </blockquote>
    <cite class="text-[10px] sm:text-xs font-bold text-secondary uppercase tracking-[0.2em]">&mdash; {{ $dailyQuote['author'] }}</cite>
</section>

<!-- Submit Obituary Call-To-Action (CTA) Section -->
<section class="w-full bg-slate-950 text-white py-16 sm:py-20 relative overflow-hidden border-t border-slate-800">
    <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 via-slate-900/50 to-primary/20 pointer-events-none"></div>
    <div class="max-w-[1000px] mx-auto px-4 sm:px-6 text-center relative z-10">
        <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/20">
            <span class="material-symbols-outlined text-[16px]">edit_document</span>
            <span>Preserve Their Memory</span>
        </div>
        <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-8 leading-tight">
            Honoring Lives With Grace & Dignity
        </h2>
        <div class="flex items-center justify-center">
            <a href="{{ route('obituaries.submit') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-8 py-4 rounded-xl text-sm sm:text-base transition-all duration-300 shadow-lg hover:shadow-amber-500/25 flex items-center justify-center space-x-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Submit Obituary Notice</span>
            </a>
        </div>
    </div>
</section>

<!-- Features / Pillars Section -->
<section class="w-full bg-surface-container-low py-14 sm:py-20 border-y border-outline-variant/10">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-12">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/5 rounded-full flex items-center justify-center mb-4 sm:mb-6 text-primary">
                    <span class="material-symbols-outlined text-[24px] sm:text-[28px]">auto_stories</span>
                </div>
                <h4 class="font-serif text-lg sm:text-xl font-bold mb-2 sm:mb-3">Digital Sanctuaries</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Create a permanent digital home for photos, stories, and family trees that will last for generations.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/5 rounded-full flex items-center justify-center mb-4 sm:mb-6 text-primary">
                    <span class="material-symbols-outlined text-[24px] sm:text-[28px]">favorite</span>
                </div>
                <h4 class="font-serif text-lg sm:text-xl font-bold mb-2 sm:mb-3">Community Tributes</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Allow friends and family from around the world to light virtual candles and leave messages of comfort.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/5 rounded-full flex items-center justify-center mb-4 sm:mb-6 text-primary">
                    <span class="material-symbols-outlined text-[24px] sm:text-[28px]">verified</span>
                </div>
                <h4 class="font-serif text-lg sm:text-xl font-bold mb-2 sm:mb-3">Verified & Dignified</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Every submission is carefully reviewed to ensure it meets our standards of respect and authenticity.</p>
            </div>
        </div>
    </div>
</section>

@endsection
