@extends('layouts.app')

@section('title', 'About Us | Obituaries.co.ke')

@section('content')
<div class="bg-slate-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-3">About Obituaries.co.ke</h1>
        <p class="text-slate-300 text-base">Preserving legacies, celebrating lives, and connecting grieving families across Kenya.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-2xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-6 text-slate-700 leading-relaxed">
        <h2 class="font-serif text-2xl font-bold text-slate-900">Our Mission</h2>
        <p>
            At <strong>Obituaries.co.ke</strong>, we believe that every life lived leaves a meaningful story behind. Our mission is to provide families and friends in Kenya with a dignified, accessible, and verified platform to announce passings, share memories, and publish funeral service details.
        </p>

        <div class="my-8 p-6 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 font-serif italic text-lg text-center">
            "Every life deserves to be remembered."
        </div>

        <h2 class="font-serif text-2xl font-bold text-slate-900">Why Choose Obituaries.co.ke</h2>
        <ul class="list-disc pl-6 space-y-3">
            <li><strong>Simple Submission Process:</strong> Anyone can submit an obituary in minutes without complex registration or technical hassle.</li>
            <li><strong>Verified Notices:</strong> Our editorial team verifies each submission to ensure dignity, authenticity, and peace of mind for families.</li>
            <li><strong>Affordable & Transparent:</strong> KES 500 fixed cost paid seamlessly via M-Pesa. No hidden charges.</li>
            <li><strong>Wide Reach:</strong> Sharable across WhatsApp, social media, and searchable by county and year forever.</li>
        </ul>

        <div class="pt-6 border-t border-slate-200">
            <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl text-sm transition-colors">
                Publish a Tribute Now
            </a>
        </div>
    </div>
</div>
@endsection
