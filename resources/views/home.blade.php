@extends('layouts.app')

@section('title', 'Obituaries.co.ke | Remembering Lives. Sharing Memories.')

@section('content')

<!-- Immersive Hero Section from Stitch Design -->
<section class="relative w-full h-[650px] min-h-[500px] flex items-center overflow-hidden">
    <!-- Cover Image with Gradient Scrim -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center animate-ken-burns" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAX9PfmGNd6COO34ia8jh6uRhqSKwIofOR8rZW8cgAnNwMH3tP1UF3CZoFM6J5iZJE5_RiTeLT8EdFGNX5813RF5aH8e_9Qh-j_Aa6PlHJ_CU5GhXpiWL3iIkjCNBE5tS_CAh8IUke7mq1LARZhVFHiHiONDoNvPG6WlbGSaF6F0dUARP99swDYvlmJF_GBO_MKQTXW0S4AD0qBEADEnppuKEEfg4E3WSxqHE-jdby2Xue9MuZu9BC8LimsaHd81IVXuLEaP8y6jFfw')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-surface via-surface/85 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-[1200px] mx-auto px-6 w-full">
        <div class="max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-[1px] w-12 bg-secondary"></div>
                <span class="text-xs font-semibold text-secondary tracking-widest uppercase">Honoring Every Journey</span>
            </div>
            
            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-primary mb-6 leading-tight">
                Remembering Lives.<br/>
                <span class="italic font-normal">Sharing Memories.</span>
            </h1>

            <p class="text-base sm:text-lg text-on-surface-variant mb-10 max-w-lg leading-relaxed">
                A dignified space to create and preserve a lasting digital sanctuary for your loved ones. We help you tell their story with the grace it deserves.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-8 py-4 rounded-xl text-xs font-semibold hover:shadow-xl transition-all duration-300 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Submit Obituary</span>
                </a>
                <a href="#search-archives" class="border border-outline text-primary px-8 py-4 rounded-xl text-xs font-semibold hover:bg-surface-container transition-all duration-300 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Search Archives</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Obituaries (Editorial Grid matching Stitch Design) -->
<section class="w-full py-20 bg-surface">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="font-serif text-3xl font-bold text-primary mb-2">Recent Tributes</h2>
                <p class="text-sm text-on-surface-variant">Honoring those who recently joined the ancestors.</p>
            </div>
            <a href="{{ route('obituaries.search') }}" class="group flex items-center gap-2 text-xs font-bold text-primary hover:text-secondary transition-colors">
                <span>View All Archives</span>
                <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
        </div>

        @if($latestObituaries->isEmpty())
            <div class="bg-surface-container-lowest rounded-2xl p-12 text-center border border-outline-variant/30 max-w-xl mx-auto">
                <span class="material-symbols-outlined text-[48px] text-on-surface-variant/40 mb-3">auto_stories</span>
                <h3 class="font-serif text-xl font-bold text-primary mb-2">No Published Obituaries Yet</h3>
                <p class="text-xs text-on-surface-variant mb-6">Be the first to publish a verified tribute for your loved one.</p>
                <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center px-6 py-3 bg-primary text-on-primary rounded-xl text-xs font-semibold">
                    Submit an Obituary
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestObituaries as $obituary)
                    <div class="group relative bg-surface-container-lowest p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-2 flex flex-col justify-between border border-outline-variant/20">
                        <div>
                            <!-- Aspect 4/5 Image -->
                            <div class="relative aspect-4/5 mb-6 overflow-hidden rounded-xl bg-surface-container">
                                @if($obituary->photo)
                                    <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-105 group-hover:scale-100">
                                @else
                                    <div class="w-full h-full bg-gradient-to-b from-primary-container to-primary flex flex-col items-center justify-center p-6 text-center text-on-primary">
                                        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center text-secondary-fixed mb-2">
                                            <span class="material-symbols-outlined text-[32px]">church</span>
                                        </div>
                                        <span class="font-serif text-sm italic">In Loving Memory</span>
                                    </div>
                                @endif

                                <div class="absolute top-3 right-3 bg-primary/80 backdrop-blur-md text-secondary-fixed text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider border border-white/10 flex items-center space-x-1">
                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                    <span>Verified</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <span class="text-[11px] font-semibold text-secondary tracking-widest uppercase mb-2 block">{{ $obituary->town }}, {{ $obituary->county }}</span>
                                <h3 class="font-serif text-xl font-bold text-primary mb-1 group-hover:text-secondary transition-colors">{{ $obituary->full_name }}</h3>
                                <p class="text-xs text-on-surface-variant/70 mb-4 italic">
                                    {{ $obituary->date_of_birth->format('Y') }} &mdash; {{ $obituary->date_of_death->format('Y') }}
                                    @if($obituary->age) ({{ $obituary->age }} Yrs) @endif
                                </p>
                                <p class="text-xs text-on-surface-variant line-clamp-3 mb-6 leading-relaxed">
                                    {{ $obituary->biography }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('obituaries.show', $obituary->slug) }}" class="w-full py-3 border border-outline-variant rounded-xl text-xs font-semibold text-primary hover:bg-primary hover:text-on-primary transition-colors text-center block">
                            Read Tribute & Service
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Search & Filter Section matching Stitch Design -->
<section class="w-full py-20 bg-primary text-on-primary overflow-hidden relative" id="search-archives">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white/5 to-transparent pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-secondary/10 rounded-full blur-[100px]"></div>

    <div class="max-w-[1200px] mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center mb-12">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold mb-4">Search the Archives</h2>
            <p class="text-sm text-primary-fixed/70">Find and honor the legacies of those who have passed across Kenya.</p>
        </div>

        <div class="bg-surface-container-lowest/10 backdrop-blur-md p-3 rounded-[2rem] shadow-2xl border border-white/10">
            <form action="{{ route('obituaries.search') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <!-- Name Search -->
                <div class="flex-1 relative flex items-center px-5 py-3.5 bg-white/5 rounded-xl md:rounded-2xl border border-white/10">
                    <span class="material-symbols-outlined text-primary-fixed/40 mr-3">search</span>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="bg-transparent border-none outline-none w-full text-sm text-on-primary placeholder-primary-fixed/40">
                </div>

                <!-- County Select -->
                <div class="flex-1 relative flex items-center px-5 py-3.5 bg-white/5 rounded-xl md:rounded-2xl border border-white/10">
                    <span class="material-symbols-outlined text-primary-fixed/40 mr-3">location_on</span>
                    <select name="county" class="bg-transparent border-none outline-none w-full text-sm text-on-primary appearance-none cursor-pointer">
                        <option value="" class="bg-slate-900 text-white">All Counties</option>
                        @foreach($counties as $c)
                            <option value="{{ $c }}" class="bg-slate-900 text-white" {{ request('county') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Select -->
                <div class="flex-1 relative flex items-center px-5 py-3.5 bg-white/5 rounded-xl md:rounded-2xl border border-white/10">
                    <span class="material-symbols-outlined text-primary-fixed/40 mr-3">calendar_month</span>
                    <select name="year" class="bg-transparent border-none outline-none w-full text-sm text-on-primary appearance-none cursor-pointer">
                        <option value="" class="bg-slate-900 text-white">All Years</option>
                        @for($y = date('Y'); $y >= 2000; $y--)
                            <option value="{{ $y }}" class="bg-slate-900 text-white" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Submit Search -->
                <button type="submit" class="bg-secondary-fixed text-on-secondary-fixed px-8 py-3.5 rounded-xl md:rounded-2xl font-bold text-xs hover:bg-secondary hover:text-white transition-all flex items-center justify-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Search Now</span>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Memorial Quote Section from Stitch Design -->
<section class="w-full py-20 flex flex-col items-center justify-center text-center px-6">
    <div class="w-16 h-16 mb-6 text-secondary/40 flex items-center justify-center">
        <span class="material-symbols-outlined text-[48px]">format_quote</span>
    </div>
    <blockquote class="font-serif text-2xl sm:text-3xl text-primary max-w-2xl mb-6 italic leading-relaxed">
        "The song is ended, but the melody lingers on."
    </blockquote>
    <cite class="text-xs font-bold text-secondary uppercase tracking-[0.2em]">&mdash; Irving Berlin</cite>
</section>

<!-- Features / Pillars Section -->
<section class="w-full bg-surface-container-low py-20 border-y border-outline-variant/10">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-primary/5 rounded-full flex items-center justify-center mb-6 text-primary">
                    <span class="material-symbols-outlined text-[28px]">auto_stories</span>
                </div>
                <h4 class="font-serif text-xl font-bold mb-3">Digital Sanctuaries</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Create a permanent digital home for photos, stories, and family trees that will last for generations.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-primary/5 rounded-full flex items-center justify-center mb-6 text-primary">
                    <span class="material-symbols-outlined text-[28px]">favorite</span>
                </div>
                <h4 class="font-serif text-xl font-bold mb-3">Community Tributes</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Allow friends and family from around the world to light virtual candles and leave messages of comfort.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-primary/5 rounded-full flex items-center justify-center mb-6 text-primary">
                    <span class="material-symbols-outlined text-[28px]">verified</span>
                </div>
                <h4 class="font-serif text-xl font-bold mb-3">Verified & Dignified</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">Every submission is carefully reviewed to ensure it meets our standards of respect and authenticity.</p>
            </div>
        </div>
    </div>
</section>

@endsection
