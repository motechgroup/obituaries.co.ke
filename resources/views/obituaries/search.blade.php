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
    </div>

    <!-- Obituary Cards Grid from Stitch Design -->
    @if($obituaries->isEmpty())
        <div class="bg-surface-container-lowest rounded-2xl p-8 sm:p-12 text-center border border-outline-variant/30 max-w-xl mx-auto">
            <span class="material-symbols-outlined text-[40px] sm:text-[48px] text-on-surface-variant/40 mb-3">search_off</span>
            <h3 class="font-serif text-lg sm:text-xl font-bold text-primary mb-2">No Matching Obituaries</h3>
            <p class="text-xs text-on-surface-variant">Try broadening your search query or clearing county/year filters.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($obituaries as $obituary)
                <a href="{{ route('obituaries.show', $obituary->slug) }}" class="group relative bg-surface-container-lowest p-5 sm:p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5 flex flex-col justify-between border border-outline-variant/20 block cursor-pointer">
                    <div>
                        <div class="relative aspect-4/5 mb-5 sm:mb-6 overflow-hidden rounded-xl bg-surface-container">
                            @if($obituary->photo)
                                <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-105 group-hover:scale-100">
                            @else
                                <div class="w-full h-full bg-gradient-to-b from-primary to-primary-container flex flex-col items-center justify-center p-6 text-center text-on-primary">
                                    <span class="material-symbols-outlined text-[28px] sm:text-[32px] text-secondary-fixed mb-1">church</span>
                                    <span class="font-serif text-xs italic">In Loving Memory</span>
                                </div>
                            @endif

                            <div class="absolute top-3 right-3 bg-primary/80 backdrop-blur-md text-secondary-fixed text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider border border-white/10 flex items-center space-x-1">
                                <span class="material-symbols-outlined text-[12px]">verified</span>
                                <span>Verified</span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="text-[10px] sm:text-[11px] font-semibold text-secondary tracking-widest uppercase mb-1.5 block">{{ $obituary->town }}, {{ $obituary->county }}</span>
                            <h3 class="font-serif text-lg sm:text-xl font-bold text-primary mb-1 group-hover:text-secondary transition-colors">{{ $obituary->full_name }}</h3>
                            <p class="text-xs text-on-surface-variant/70 italic">
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
