@extends('layouts.app')

@section('title', $article['title'] . ' | Obituaries.co.ke')
@section('meta_description', $article['meta_description'])
@section('seo_keywords', $article['keywords'])
@section('canonical_url', url('/blog/' . $article['slug']))

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $article['title'] }}",
  "description": "{{ $article['meta_description'] }}",
  "author": {
    "@type": "Organization",
    "name": "{{ $article['author'] }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Obituaries.co.ke",
    "logo": "{{ asset('images/logo.png') }}"
  },
  "datePublished": "{{ $article['published_at'] }}",
  "url": "{{ url('/blog/' . $article['slug']) }}"
}
</script>
@endsection

@section('content')
<div class="bg-surface-container-low py-10 sm:py-14 border-b border-outline-variant/20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center space-x-2 text-xs font-bold text-on-surface-variant mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/blog') }}" class="hover:text-primary transition-colors">Guides</a>
            <span>&rsaquo;</span>
            <span class="text-primary truncate max-w-xs">{{ $article['title'] }}</span>
        </nav>

        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-primary tracking-tight leading-tight">
            {{ $article['title'] }}
        </h1>

        <div class="flex items-center space-x-4 mt-4 text-xs text-on-surface-variant font-medium">
            <span>By {{ $article['author'] }}</span>
            <span>&bull;</span>
            <span>Published {{ \Carbon\Carbon::parse($article['published_at'])->format('F j, Y') }}</span>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-10 border border-outline-variant/30 shadow-xs text-on-surface leading-relaxed text-sm sm:text-base space-y-6">
        {!! $article['content'] !!}
    </div>

    <!-- Article Bottom CTA -->
    <div class="mt-12 p-8 bg-amber-50 rounded-2xl border border-amber-200 text-center space-y-3">
        <h3 class="font-serif text-xl font-bold text-amber-900">Publish a Verified Obituary Notice</h3>
        <p class="text-xs sm:text-sm text-amber-800 max-w-xl mx-auto">
            Honoring lives with grace and dignity. Publish your obituary notice online to reach family and friends in Kenya and worldwide.
        </p>
        <div>
            <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center space-x-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Submit Obituary Notice</span>
            </a>
        </div>
    </div>
</div>
@endsection
