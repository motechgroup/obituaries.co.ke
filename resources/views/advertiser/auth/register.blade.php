@extends('layouts.advertiser')

@section('title', 'Register Advertiser Account')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-12">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 mx-auto flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[28px]">storefront</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-white">Create Advertiser Account</h1>
            <p class="text-xs text-slate-400">Promote your funeral services on Kenya's premier digital obituary portal.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-950/60 border border-rose-800 rounded-2xl text-xs text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('advertiser.register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Business Name <span class="text-amber-400">*</span></label>
                <input type="text" name="business_name" value="{{ old('business_name') }}" required placeholder="e.g. Lee Funeral Home & Hearse Services" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Contact Person Name <span class="text-amber-400">*</span></label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="e.g. James Lee" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Phone / M-Pesa Number <span class="text-amber-400">*</span></label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="e.g. 0722112233" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Email Address <span class="text-amber-400">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. info@leefuneral.co.ke" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Password <span class="text-amber-400">*</span></label>
                <input type="password" name="password" required placeholder="At least 8 characters" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Confirm Password <span class="text-amber-400">*</span></label>
                <input type="password" name="password_confirmation" required placeholder="Re-type password" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg">
                Create Advertiser Account &rarr;
            </button>
        </form>

        <div class="pt-4 border-t border-slate-800 text-center text-xs text-slate-400">
            Already registered? <a href="{{ route('advertiser.login') }}" class="text-amber-400 font-bold hover:underline">Log in to portal</a>
        </div>
    </div>
</div>
@endsection
