<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | Obituaries.co.ke Admin</title>

    <!-- Favicon & Site Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-100">

<div class="w-full max-w-md space-y-8 bg-slate-900/80 p-8 rounded-3xl border border-slate-800 shadow-2xl backdrop-blur-md">
    <div class="text-center space-y-2">
        <a href="{{ route('home') }}" class="inline-block mb-3">
            <img src="{{ asset('images/logo-light.svg') }}" alt="Obituaries.co.ke" class="h-12 w-auto mx-auto object-contain">
        </a>
        <h1 class="font-serif text-2xl font-bold tracking-tight text-white">Create New Password</h1>
        <p class="text-slate-400 text-xs">Enter your email and choose a strong new password for your admin account.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Admin Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required class="w-full px-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">New Password</label>
            <input type="password" name="password" id="password" required autofocus placeholder="Minimum 6 characters" class="w-full px-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Re-enter password" class="w-full px-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <button type="submit" class="w-full py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-amber-500/20">
            Reset & Save New Password
        </button>
    </form>
</div>

</body>
</html>
