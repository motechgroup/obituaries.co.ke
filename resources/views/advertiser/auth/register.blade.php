@extends('layouts.advertiser')

@section('title', 'Register Advertiser Account')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-12">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-900 mx-auto flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[32px]">storefront</span>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Create Advertiser Account</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Promote your funeral services on Kenya's premier digital obituary portal.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('advertiser.register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Name <span class="text-rose-500">*</span></label>
                <input type="text" name="business_name" value="{{ old('business_name') }}" required placeholder="e.g. Lee Funeral Home & Hearse Services" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person Name <span class="text-rose-500">*</span></label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="e.g. James Lee" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone / M-Pesa Number <span class="text-rose-500">*</span></label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="e.g. 0722112233" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. info@leefuneral.co.ke" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required placeholder="At least 8 characters" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Confirm Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password_confirmation" required placeholder="Re-type password" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                <span>Create Advertiser Account &rarr;</span>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-600 font-medium">
            Already registered? <a href="{{ route('advertiser.login') }}" class="text-amber-900 font-bold hover:underline">Log in to portal</a>
        </div>
    </div>
</div>
@endsection
