@extends('layouts.app')

@section('title', 'Obituaries.co.ke | Remembering Lives. Sharing Memories.')

@section('content')

<!-- Hero Section with Deep Navy Background -->
<section class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white relative overflow-hidden py-16 sm:py-24">
    <!-- Subtle Ambient Pattern -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#F59E0B_1px,transparent_1px)] [background-size:24px_24px]"></div>
    
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-medium mb-6">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <span>Dignified & Verified Digital Tributes</span>
        </div>

        <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-white mb-6 leading-tight">
            Remembering Lives.<br class="hidden sm:inline"> Sharing Memories.
        </h1>
        
        <p class="text-slate-300 text-base sm:text-lg md:text-xl font-normal max-w-2xl mx-auto mb-10 leading-relaxed">
            Create and preserve a lasting tribute for your loved ones. Simple, verified, and accessible across Kenya.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('obituaries.submit') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-semibold bg-amber-600 hover:bg-amber-500 text-white shadow-lg shadow-amber-600/25 transition-all duration-200">
                <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Submit Obituary
            </a>

            <a href="#search-section" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-medium bg-slate-800/90 hover:bg-slate-700/90 text-slate-200 border border-slate-700/80 transition-all duration-200">
                <svg class="w-5 h-5 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search Obituaries
            </a>
        </div>
    </div>
</section>

<!-- Search Section -->
<section id="search-section" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        <form action="{{ route('obituaries.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <!-- Name Input -->
            <div class="sm:col-span-5">
                <label for="search-name" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Deceased Name</label>
                <div class="relative">
                    <input type="text" name="name" id="search-name" placeholder="Search by full name..." class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <!-- County Select -->
            <div class="sm:col-span-4">
                <label for="search-county" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">County</label>
                <select name="county" id="search-county" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                    <option value="">All Counties</option>
                    @foreach($counties as $county)
                        <option value="{{ $county }}">{{ $county }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Select -->
            <div class="sm:col-span-3">
                <label for="search-year" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Year</label>
                <select name="year" id="search-year" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                    <option value="">All Years</option>
                    @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Submit Button -->
            <div class="sm:col-span-12 mt-2">
                <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-all shadow-md flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Search Obituary Directory</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Latest Obituaries Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 border-b border-slate-200 pb-6">
        <div>
            <span class="text-xs uppercase tracking-widest font-semibold text-amber-600 block mb-1">Recent Notices</span>
            <h2 class="font-serif text-3xl font-bold text-slate-900">Latest Verified Obituaries</h2>
        </div>
        <a href="{{ route('obituaries.search') }}" class="mt-4 md:mt-0 text-sm font-semibold text-amber-700 hover:text-amber-800 inline-flex items-center group">
            <span>View All Directory Notices</span>
            <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>

    @if($latestObituaries->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"/>
                </svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">No Published Obituaries Yet</h3>
            <p class="text-slate-500 text-sm mb-6">Be the first to publish a verified tribute for your loved one.</p>
            <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center px-5 py-2.5 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-500 transition-colors">
                Submit an Obituary
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($latestObituaries as $obituary)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group">
                    <!-- Photo Header -->
                    <div class="relative h-64 bg-slate-100 overflow-hidden">
                        @if($obituary->photo)
                            <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex flex-col items-center justify-center p-6 text-center text-slate-300">
                                <div class="w-16 h-16 rounded-full bg-slate-700/60 border border-slate-600/50 flex items-center justify-center text-amber-400 mb-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-serif text-slate-400 italic text-sm">In Loving Memory</span>
                            </div>
                        @endif

                        <!-- Badge -->
                        <div class="absolute top-4 right-4 bg-slate-900/80 backdrop-blur-md text-amber-400 text-xs px-3 py-1 rounded-full font-medium border border-slate-700/50 flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Verified</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-serif text-xl font-bold text-slate-900 group-hover:text-amber-700 transition-colors mb-2 leading-snug">
                                {{ $obituary->full_name }}
                            </h3>

                            <!-- Dates -->
                            <div class="flex items-center text-xs font-semibold text-amber-700 uppercase tracking-wider mb-4 space-x-2">
                                <span>{{ $obituary->date_of_birth->format('M d, Y') }}</span>
                                <span>&mdash;</span>
                                <span>{{ $obituary->date_of_death->format('M d, Y') }}</span>
                                @if($obituary->age)
                                    <span class="text-slate-400 font-normal">({{ $obituary->age }} Yrs)</span>
                                @endif
                            </div>

                            <!-- Location -->
                            <div class="flex items-center text-xs text-slate-500 mb-4">
                                <svg class="w-4 h-4 mr-1.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $obituary->town }}, {{ $obituary->county }} County</span>
                            </div>

                            <!-- Bio snippet -->
                            <p class="text-slate-600 text-sm line-clamp-3 leading-relaxed mb-6">
                                {{ $obituary->biography }}
                            </p>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('obituaries.show', $obituary->slug) }}" class="w-full py-2.5 px-4 bg-slate-50 hover:bg-amber-50 text-slate-800 hover:text-amber-900 border border-slate-200 hover:border-amber-200 rounded-xl text-sm font-semibold text-center transition-all duration-200 flex items-center justify-center space-x-2">
                            <span>View Full Obituary & Tribute</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Values & Assurance Banner -->
<section class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-800">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg font-bold text-white mb-2">Verified Submissions</h3>
                <p class="text-sm text-slate-400">Every obituary is reviewed by our administration team to confirm authenticity and respect.</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-800">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg font-bold text-white mb-2">Instant M-Pesa Checkout</h3>
                <p class="text-sm text-slate-400">Convenient KES 500 fixed fee via Safaricom M-Pesa STK push direct to your mobile phone.</p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/40 border border-slate-800">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg font-bold text-white mb-2">Easy Family Sharing</h3>
                <p class="text-sm text-slate-400">One-click sharing via WhatsApp and Facebook so friends and family near and far can honor the memory.</p>
            </div>
        </div>
    </div>
</section>

@endsection
