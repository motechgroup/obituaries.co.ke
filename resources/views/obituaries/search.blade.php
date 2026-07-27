@extends('layouts.app')

@section('title', 'Search Obituary Directory | Obituaries.co.ke')

@section('content')
<!-- Header Banner -->
<div class="bg-primary text-on-primary py-10 sm:py-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 text-center">
        <h1 class="font-serif text-3xl sm:text-5xl font-bold mb-2 sm:mb-3">Obituary Directory</h1>
        <p class="text-primary-fixed/70 text-xs sm:text-base max-w-xl mx-auto">
            Search verified obituaries, life stories, and funeral services across Kenya.
        </p>
    </div>
</div>

<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8 sm:space-y-12">
    
    <!-- Filter Card from Stitch Design -->
    <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl border border-outline-variant/30 shadow-md">
        <form action="{{ route('obituaries.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
            <!-- Name Input -->
            <div class="sm:col-span-5">
                <label for="name" class="block text-[10px] sm:text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 sm:mb-2">Deceased Name</label>
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 border border-outline-variant focus-within:border-primary">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[18px] sm:text-[20px]">search</span>
                    <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="Search by name..." class="bg-transparent border-none outline-none w-full text-xs text-on-surface">
                </div>
            </div>

            <!-- County Select -->
            <div class="sm:col-span-3">
                <label for="county" class="block text-[10px] sm:text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 sm:mb-2">County</label>
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 border border-outline-variant">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[18px] sm:text-[20px]">location_on</span>
                    <select name="county" id="county" class="bg-transparent border-none outline-none w-full text-xs text-on-surface appearance-none cursor-pointer">
                        <option value="">All Counties</option>
                        @foreach($counties as $c)
                            <option value="{{ $c }}" {{ request('county') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Year Select -->
            <div class="sm:col-span-2">
                <label for="year" class="block text-[10px] sm:text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 sm:mb-2">Year</label>
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 border border-outline-variant">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[18px] sm:text-[20px]">calendar_month</span>
                    <select name="year" id="year" class="bg-transparent border-none outline-none w-full text-xs text-on-surface appearance-none cursor-pointer">
                        <option value="">All Years</option>
                        @for($y = date('Y'); $y >= 2000; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 flex gap-2 pt-1 sm:pt-0">
                <button type="submit" class="w-full py-2.5 sm:py-3 bg-primary text-on-primary font-bold rounded-xl text-xs hover:bg-primary-container transition-colors">
                    Filter
                </button>
                <a href="{{ route('obituaries.search') }}" class="px-3.5 sm:px-4 py-2.5 sm:py-3 bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold rounded-xl text-xs transition-colors flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>

        <!-- Quick Filter Pills -->
        <div class="mt-4 flex items-center space-x-2 overflow-x-auto text-xs font-semibold">
            <a href="{{ route('obituaries.search') }}" class="px-3.5 py-1.5 rounded-full border {{ empty(request('filter')) ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container-low text-on-surface-variant border-outline-variant hover:bg-surface-container' }}">
                All Tributes
            </a>
            <a href="{{ route('obituaries.search', ['filter' => 'anniversaries']) }}" class="px-3.5 py-1.5 rounded-full border flex items-center space-x-1 {{ request('filter') === 'anniversaries' ? 'bg-amber-800 text-white border-amber-800' : 'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100' }}">
                <span>🌹 Today's Anniversaries</span>
            </a>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-6 flex items-center justify-between">
        <p class="text-xs sm:text-sm text-on-surface-variant">
            @if(request('filter') === 'anniversaries')
                Showing <strong class="text-primary font-bold">{{ $obituaries->total() }}</strong> obituaries with anniversaries of passing today.
            @else
                Showing <strong class="text-primary font-bold">{{ $obituaries->total() }}</strong> published obituaries.
            @endif
        </p>
    </div>

    @if($obituaries->isEmpty())
        <div class="bg-surface-container-lowest rounded-2xl p-12 text-center border border-outline-variant/30 max-w-xl mx-auto">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant/40 mb-3">search_off</span>
            <h3 class="font-serif text-xl font-bold text-primary mb-2">No Obituaries Found</h3>
            <p class="text-xs text-on-surface-variant mb-6">Try refining your search terms or clearing your filter criteria.</p>
            <a href="{{ route('obituaries.search') }}" class="inline-flex items-center px-6 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-semibold">
                Clear Filters
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-6">
            @foreach($obituaries as $obituary)
                <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group relative bg-surface-container-lowest p-3.5 sm:p-6 rounded-xl sm:rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5 flex flex-col justify-between border border-outline-variant/20 block cursor-pointer">
                    <div>
                        <!-- Aspect 4/5 Image -->
                        <div class="relative aspect-4/5 mb-3 sm:mb-6 overflow-hidden rounded-lg sm:rounded-xl bg-surface-container">
                            @if($obituary->photo)
                                <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-105 group-hover:scale-100">
                            @else
                                <div class="w-full h-full bg-gradient-to-b from-primary-container to-primary flex flex-col items-center justify-center p-3 sm:p-6 text-center text-on-primary">
                                    <div class="w-10 h-10 sm:w-16 sm:h-16 rounded-full bg-white/10 flex items-center justify-center text-secondary-fixed mb-1 sm:mb-2">
                                        <span class="material-symbols-outlined text-[20px] sm:text-[32px]">church</span>
                                    </div>
                                    <span class="font-serif text-[10px] sm:text-xs italic">In Loving Memory</span>
                                </div>
                            @endif

                            @if($obituary->is_anniversary_today)
                                <div class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-amber-700/90 backdrop-blur-md text-amber-100 text-[8px] sm:text-[10px] px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full font-bold uppercase tracking-wider border border-amber-300/30 flex items-center space-x-1 shadow-md z-10">
                                    <span>{{ $obituary->anniversary_badge_text }}</span>
                                </div>
                            @endif

                            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-primary/80 backdrop-blur-md text-secondary-fixed text-[8px] sm:text-[10px] px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full font-bold uppercase tracking-wider border border-white/10 flex items-center space-x-0.5 sm:space-x-1">
                                <span class="material-symbols-outlined text-[10px] sm:text-[12px]">verified</span>
                                <span>Verified</span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="text-[9px] sm:text-[11px] font-semibold text-secondary tracking-widest uppercase mb-0.5 sm:mb-1 block truncate">{{ $obituary->town }}, {{ $obituary->county }}</span>
                            <h3 class="font-serif text-sm sm:text-xl font-bold text-primary mb-0.5 sm:mb-1 group-hover:text-secondary transition-colors line-clamp-2 leading-snug">{{ $obituary->full_name }}</h3>
                            <p class="text-[10px] sm:text-xs text-on-surface-variant/70 italic">
                                {{ $obituary->date_of_birth->format('Y') }} &mdash; {{ $obituary->date_of_death->format('Y') }}
                                @if($obituary->age) ({{ $obituary->age }} Yrs) @endif
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $obituaries->links() }}
        </div>
    @endif
</div>
@endsection
