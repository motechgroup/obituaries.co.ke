<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Obituaries.co.ke</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen p-4 antialiased selection:bg-amber-500 selection:text-white">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border border-slate-200">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="font-serif text-2xl font-bold text-slate-900 block">
                Obituaries<span class="text-amber-600">.co.ke</span>
            </a>
            <span class="text-xs uppercase tracking-widest text-slate-400 font-semibold mt-1 block">Administrator Sign In</span>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Admin Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', 'admin@obituaries.co.ke') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required value="password123" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-amber-600 rounded border-slate-300">
                    <span>Remember Me</span>
                </label>
                <a href="{{ route('admin.password.request') }}" class="text-amber-700 hover:underline font-semibold">Forgot password?</a>
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm transition-all shadow-md">
                Sign In to Admin Dashboard
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-200 text-center">
            <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Public Platform</a>
        </div>
    </div>

</body>
</html>
