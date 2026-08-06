@extends('layouts.app')

@section('title', 'Kenyan Counties Obituary Registry | Obituaries.co.ke')
@section('meta_description', 'Browse obituary notices, funeral service schedules, and memorial tributes across all 47 counties of Kenya. Select your county to view local registries.')
@section('seo_keywords', 'kenya obituaries by county, nairobi obituaries, kiambu obituaries, mombasa obituaries, nakuru obituaries, machakos obituaries')

@section('content')
<!-- Hero Header Section -->
<div class="bg-surface-container-low py-10 sm:py-14 border-b border-outline-variant/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-bold text-on-surface-variant mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ route('obituaries.search') }}" class="hover:text-primary transition-colors">Obituaries Directory</a>
            <span>&rsaquo;</span>
            <span class="text-primary">Kenyan Counties</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider mb-3 border border-amber-300/30">
                    <span class="material-symbols-outlined text-[16px]">map</span>
                    <span>All 47 Counties of Kenya</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-primary tracking-tight">
                    Kenyan Counties Obituary Registry
                </h1>
                <p class="mt-3 text-sm sm:text-base text-on-surface-variant max-w-3xl leading-relaxed">
                    Browse memorial notices, passing announcements, and funeral service schedules listed across Kenya's 47 regional counties. Select any county below to explore its local directory.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <x-ad-banner placement="homepage_header" />
</div>

<!-- Counties Directory Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14" x-data="{ search: '' }">
    <!-- Search / Filter Input -->
    <div class="max-w-md mx-auto mb-10">
        <div class="relative flex items-center bg-surface-container-lowest rounded-2xl px-4 py-3 border border-outline-variant/40 shadow-xs">
            <span class="material-symbols-outlined text-on-surface-variant/60 mr-3 text-[20px]">search</span>
            <input type="text" x-model="search" placeholder="Type county name to filter (e.g. Kiambu, Nairobi)..." class="bg-transparent border-none outline-none w-full text-sm text-primary placeholder-on-surface-variant/50 font-medium">
        </div>
    </div>

    <!-- Counties Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
        @foreach($counties as $c)
            <a href="{{ route('obituaries.county', $c['slug']) }}"
               x-show="search === '' || '{{ strtolower($c['name']) }}'.includes(search.toLowerCase())"
               class="group bg-surface-container-lowest hover:bg-slate-900 p-4 rounded-2xl border border-outline-variant/20 hover:border-slate-800 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between items-center text-center">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 group-hover:bg-amber-500/20 text-amber-800 group-hover:text-amber-400 flex items-center justify-center mb-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                </div>
                <div>
                    <h3 class="font-serif text-sm sm:text-base font-bold text-primary group-hover:text-white transition-colors leading-tight mb-1">
                        {{ $c['name'] }}
                    </h3>
                    <span class="text-[10px] font-semibold text-secondary group-hover:text-amber-400 transition-colors uppercase tracking-wider block">
                        {{ $c['count'] }} {{ Str::plural('Notice', $c['count']) }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
