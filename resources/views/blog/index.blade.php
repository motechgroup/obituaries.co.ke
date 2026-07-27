@extends('layouts.app')

@section('title', 'Kenyan Obituary & Funeral Guides | Obituaries.co.ke Blog')
@section('meta_description', 'Guides, advice, and cultural insights on writing obituaries, organizing funeral services, and honoring loved ones in Kenya.')
@section('seo_keywords', 'how to write an obituary Kenya, funeral guides Kenya, Kenya death notices advice')

@section('content')
<div class="bg-surface-container-low py-12 sm:py-16 border-b border-outline-variant/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-xs font-bold text-on-surface-variant mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span>&rsaquo;</span>
            <span class="text-primary">Guides & Advice</span>
        </nav>

        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-500/10 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider mb-3 border border-amber-300/30">
                <span class="material-symbols-outlined text-[16px]">menu_book</span>
                <span>Obituary & Funeral Guides</span>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-primary tracking-tight">
                Resource Center & Guides for Kenya
            </h1>
            <p class="mt-3 text-sm sm:text-base text-on-surface-variant leading-relaxed">
                Empowering Kenyan families with guidance on writing respectful obituaries, organizing funeral announcements, and preserving cherished memories.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($articles as $art)
            <article class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center space-x-2 text-amber-800 text-[11px] font-bold uppercase tracking-wider mb-3">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                        <span>{{ \Carbon\Carbon::parse($art['published_at'])->format('M d, Y') }}</span>
                    </div>

                    <h2 class="font-serif text-xl font-bold text-primary hover:text-amber-800 transition-colors mb-3 leading-snug">
                        <a href="{{ url('/blog/' . $art['slug']) }}">{{ $art['title'] }}</a>
                    </h2>

                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-3 mb-4">
                        {{ $art['meta_description'] }}
                    </p>
                </div>

                <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-xs font-bold text-primary hover:text-amber-800">
                        <a href="{{ url('/blog/' . $art['slug']) }}" class="flex items-center space-x-1">
                            <span>Read Full Guide</span>
                            <span>&rarr;</span>
                        </a>
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    <!-- Call to Action Banner -->
    <div class="mt-16 bg-slate-950 text-white rounded-3xl p-8 sm:p-12 text-center relative overflow-hidden shadow-xl border border-slate-800">
        <div class="max-w-2xl mx-auto relative z-10 space-y-4">
            <h2 class="font-serif text-2xl sm:text-3xl font-bold">Ready to Publish an Official Obituary Notice?</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Publish a verified obituary notice to notify family, receive virtual candles, and share downloadable funeral programmes worldwide.
            </p>
            <div class="pt-2">
                <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-8 py-3.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Submit Obituary Notice</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
