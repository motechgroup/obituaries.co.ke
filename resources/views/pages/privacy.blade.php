@extends('layouts.app')

@section('title', 'Privacy Policy | Obituaries.co.ke')

@section('content')
<div class="bg-slate-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold mb-3">Privacy Policy</h1>
        <p class="text-slate-300 text-base">How we handle and safeguard submitter and tribute data.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-2xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-6 text-slate-700 leading-relaxed text-sm">
        <h2 class="font-serif text-xl font-bold text-slate-900">1. Data Collection</h2>
        <p>We collect submitter details (name, phone number, relationship, and optional email) solely for verification, administrative recordkeeping, and payment reconciliation.</p>

        <h2 class="font-serif text-xl font-bold text-slate-900">2. Minimal Sensitive Information</h2>
        <p>We do not require or store sensitive identification documents (such as National IDs) on our public servers unless explicitly necessary for fraud prevention.</p>

        <h2 class="font-serif text-xl font-bold text-slate-900">3. Payment Information</h2>
        <p>All financial transactions are conducted directly through Safaricom M-Pesa API interfaces. We store transaction IDs, receipt numbers, and phone numbers for verification.</p>

        <h2 class="font-serif text-xl font-bold text-slate-900">4. Contact Us</h2>
        <p>If you have any questions regarding your data or wish to request amendments, contact us at privacy@obituaries.co.ke.</p>
    </div>
</div>
@endsection
