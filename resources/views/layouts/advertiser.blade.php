<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Advertiser Portal') | Obituaries.co.ke</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .font-serif { font-family: 'Cinzel', serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-sans h-full bg-slate-950 text-slate-100 antialiased flex flex-col min-h-screen selection:bg-amber-500 selection:text-slate-950">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('advertiser.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-bold group-hover:bg-amber-500 group-hover:text-slate-950 transition-all">
                        <span class="material-symbols-outlined text-[20px]">campaign</span>
                    </div>
                    <div>
                        <span class="font-serif font-bold text-base tracking-wider text-white block">Obituaries.co.ke</span>
                        <span class="text-[10px] text-amber-400 font-bold uppercase tracking-widest block">Advertiser Portal</span>
                    </div>
                </a>

                @auth('advertiser')
                    <nav class="hidden md:flex items-center space-x-1 text-xs font-semibold">
                        <a href="{{ route('advertiser.dashboard') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.dashboard') ? 'bg-amber-500/10 text-amber-400 font-bold border border-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('advertiser.campaigns.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.campaigns.*') ? 'bg-amber-500/10 text-amber-400 font-bold border border-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            My Campaigns
                        </a>
                        <a href="{{ route('advertiser.profile.edit') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.profile.*') ? 'bg-amber-500/10 text-amber-400 font-bold border border-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Business Profile
                        </a>
                        <a href="{{ route('advertiser.analytics.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.analytics.*') ? 'bg-amber-500/10 text-amber-400 font-bold border border-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Performance Analytics
                        </a>
                    </nav>
                @endauth
            </div>

            <div class="flex items-center space-x-3">
                @auth('advertiser')
                    <a href="{{ route('advertiser.campaigns.create') }}" class="hidden sm:inline-flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        <span>New Ad Campaign</span>
                    </a>

                    <!-- User Menu Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr(Auth::guard('advertiser')->user()->business_name, 0, 1)) }}
                            </span>
                            <span class="max-w-[120px] truncate hidden sm:inline">{{ Auth::guard('advertiser')->user()->business_name }}</span>
                            <span class="material-symbols-outlined text-[14px]">expand_more</span>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-xl shadow-xl py-2 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-800">
                                <p class="font-bold text-white truncate">{{ Auth::guard('advertiser')->user()->business_name }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::guard('advertiser')->user()->email }}</p>
                            </div>
                            <a href="{{ route('advertiser.profile.edit') }}" class="block px-4 py-2 text-slate-300 hover:text-amber-400 hover:bg-slate-800">Edit Business Profile</a>
                            <a href="{{ route('advertiser.campaigns.index') }}" class="block px-4 py-2 text-slate-300 hover:text-amber-400 hover:bg-slate-800">Manage Campaigns</a>
                            <a href="/" target="_blank" class="block px-4 py-2 text-slate-400 hover:text-white hover:bg-slate-800 border-t border-slate-800 mt-1">View Public Site ↗</a>
                            <form action="{{ route('advertiser.logout') }}" method="POST" class="border-t border-slate-800 mt-1">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 hover:bg-rose-950/40">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('advertiser.login') }}" class="text-xs font-semibold text-slate-300 hover:text-white px-3 py-2">Log In</a>
                    <a href="{{ route('advertiser.register') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">Register Business</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Body Container -->
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs rounded-2xl font-bold flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[20px] text-emerald-400">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-950/80 border border-rose-800 text-rose-300 text-xs rounded-2xl font-bold flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[20px] text-rose-400">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>© {{ date('Y') }} Obituaries.co.ke Advertising Portal. All rights reserved.</p>
    </footer>

</body>
</html>
