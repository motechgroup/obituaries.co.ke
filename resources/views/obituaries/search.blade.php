@extends('layouts.app')

@section('title', 'Search Obituary Directory | Obituaries.co.ke')

@section('content')
<!-- Header Banner -->
<div class="bg-slate-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-2">Obituary Directory</h1>
        <p class="text-slate-300 text-sm sm:text-base">Search verified obituaries and tributes across Kenya.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    
    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <form action="{{ route('obituaries.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <!-- Name Input -->
            <div class="sm:col-span-5">
                <label for="name" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Deceased Name</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:ring-2 focus:ring-amber-500">
            </div>

            <!-- County Select -->
            <div class="sm:col-span-3">
                <label for="county" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">County</label>
                <select name="county" id="county" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:ring-2 focus:ring-amber-500">
                    <option value="">All Counties</option>
                    @foreach($counties as $c)
                        <option value="{{ $c }}" {{ request('county') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Select -->
            <div class="sm:col-span-2">
                <label for="year" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Year</label>
                <select name="year" id="year" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:ring-2 focus:ring-amber-500">
                    <option value="">All Years</option>
                    @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="sm:col-span-2 flex space-x-2">
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-colors">
                    Filter
                </button>
                <a href="{{ route('obituaries.search') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Obituary Cards Grid -->
    @if($obituaries->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">No Matching Obituaries</h3>
            <p class="text-slate-500 text-sm">Try broadening your search query or removing county filters.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($obituaries as $obituary)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative h-60 bg-slate-100 overflow-hidden">
                        @if($obituary->photo)
                            <img src="{{ asset('storage/' . $obituary->photo) }}" alt="{{ $obituary->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-slate-900 flex flex-col items-center justify-center p-6 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 mb-2">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-serif text-xs italic">In Loving Memory</span>
                            </div>
                        @endif

                        <div class="absolute top-4 right-4 bg-slate-900/80 backdrop-blur-md text-amber-400 text-xs px-3 py-1 rounded-full font-medium border border-slate-700/50 flex items-center space-x-1">
                            <span>Verified</span>
                        </div>
                    </div>

                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-serif text-xl font-bold text-slate-900 group-hover:text-amber-700 transition-colors mb-2">
                                {{ $obituary->full_name }}
                            </h3>

                            <div class="flex items-center text-xs font-semibold text-amber-700 uppercase tracking-wider mb-4 space-x-2">
                                <span>{{ $obituary->date_of_birth->format('M d, Y') }}</span>
                                <span>&mdash;</span>
                                <span>{{ $obituary->date_of_death->format('M d, Y') }}</span>
                                @if($obituary->age)
                                    <span class="text-slate-400 font-normal">({{ $obituary->age }} Yrs)</span>
                                @endif
                            </div>

                            <div class="flex items-center text-xs text-slate-500 mb-4">
                                <svg class="w-4 h-4 mr-1.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span>{{ $obituary->town }}, {{ $obituary->county }} County</span>
                            </div>

                            <p class="text-slate-600 text-sm line-clamp-3 leading-relaxed mb-6">
                                {{ $obituary->biography }}
                            </p>
                        </div>

                        <a href="{{ route('obituaries.show', $obituary->slug) }}" class="w-full py-2.5 px-4 bg-slate-50 hover:bg-amber-50 text-slate-800 hover:text-amber-900 border border-slate-200 hover:border-amber-200 rounded-xl text-sm font-semibold text-center transition-all flex items-center justify-center space-x-2">
                            <span>View Full Obituary</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $obituaries->links() }}
        </div>
    @endif
</div>
@endsection
