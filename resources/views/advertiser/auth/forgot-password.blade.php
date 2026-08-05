@extends('layouts.app')

@section('title', 'Forgot Password | Advertiser Portal')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke" class="h-10 w-auto mx-auto">
            </a>
            <h2 class="font-serif text-2xl font-bold text-slate-900">Forgot Password?</h2>
            <p class="text-xs text-slate-600 font-medium">Enter your registered advertiser email to receive password reset instructions.</p>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-bold text-rose-800 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('advertiser.password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Advertiser Email Address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="advertiser@company.co.ke" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                    Send Password Reset Link
                </button>
            </div>
        </form>

        <div class="text-center pt-4 border-t border-slate-100 text-xs">
            <a href="{{ route('advertiser.login') }}" class="font-bold text-amber-900 hover:underline">
                &larr; Back to Advertiser Login
            </a>
        </div>
    </div>
</div>
@endsection
