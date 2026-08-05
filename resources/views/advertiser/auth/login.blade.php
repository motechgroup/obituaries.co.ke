@extends('layouts.advertiser')

@section('title', 'Advertiser Login')

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-16">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-900 mx-auto flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[32px]">campaign</span>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900">Advertiser Login</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-600">Access your advertising dashboard, campaigns, and M-Pesa statements.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1 font-medium">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('advertiser.login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="advertiser@domain.co.ke" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Enter password" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium focus:bg-white focus:border-amber-500 outline-none transition-all">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 text-slate-700 font-medium cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span>Remember Me</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                <span>Log In to Advertiser Portal &rarr;</span>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-600 font-medium">
            Don't have an advertiser account? <a href="{{ route('advertiser.register') }}" class="text-amber-900 font-bold hover:underline">Register your business</a>
        </div>
    </div>
</div>
@endsection
