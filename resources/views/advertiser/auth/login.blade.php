@extends('layouts.advertiser')

@section('title', 'Advertiser Login')

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-16">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 mx-auto flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[28px]">lock</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-white">Advertiser Login</h1>
            <p class="text-xs text-slate-400">Access your advertising dashboard, campaigns, and M-Pesa statements.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-950/60 border border-rose-800 rounded-2xl text-xs text-rose-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('advertiser.login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="advertiser@domain.co.ke" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:border-amber-500 outline-none">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-amber-500 focus:ring-amber-500">
                    <span>Remember Me</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg">
                Log In to Portal &rarr;
            </button>
        </form>

        <div class="pt-4 border-t border-slate-800 text-center text-xs text-slate-400">
            Don't have an advertiser account? <a href="{{ route('advertiser.register') }}" class="text-amber-400 font-bold hover:underline">Register your business</a>
        </div>
    </div>
</div>
@endsection
