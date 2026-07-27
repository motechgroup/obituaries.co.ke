@extends('layouts.app')

@section('title', 'Contact Us | Obituaries.co.ke')

@section('content')
<div class="bg-slate-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-3">Contact Support</h1>
        <p class="text-slate-300 text-base">We are here to assist you during your time of need.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm space-y-6">
            <h2 class="font-serif text-2xl font-bold text-slate-900">Get in Touch</h2>
            <p class="text-slate-600 text-sm leading-relaxed">
                If you need assistance with an obituary submission, payment verification, or editorial edits, reach out to our team via phone or email.
            </p>

            <div class="space-y-4 text-sm text-slate-700">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <div>
                        <span class="font-semibold block text-slate-900">Phone Support</span>
                        <span>+254 700 000 000</span>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <span class="font-semibold block text-slate-900">Email Support</span>
                        <span>support@obituaries.co.ke</span>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <span class="font-semibold block text-slate-900">Location</span>
                        <span>Nairobi, Kenya</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
            <h2 class="font-serif text-2xl font-bold text-slate-900 mb-6">Send Message</h2>
            <form action="#" @submit.prevent="alert('Thank you for reaching out. We will get back to you shortly.')" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Your Name</label>
                    <input type="text" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Phone or Email</label>
                    <input type="text" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Message</label>
                    <textarea rows="4" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-colors">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
