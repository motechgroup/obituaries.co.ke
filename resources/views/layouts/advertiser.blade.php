<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Advertiser Portal') | Obituaries.co.ke</title>

    <!-- Favicon & Site Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans h-full bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen selection:bg-amber-500 selection:text-slate-950">

    <!-- Header Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke Logo" class="h-9 w-auto object-contain">
                    <span class="text-[10px] bg-amber-100 text-amber-900 border border-amber-300 font-extrabold uppercase px-2 py-0.5 rounded-full tracking-wider">
                        Advertiser Portal
                    </span>
                </a>

                @auth('advertiser')
                    <nav class="hidden md:flex items-center space-x-1 text-xs font-semibold">
                        <a href="{{ route('advertiser.dashboard') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('advertiser.campaigns.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.campaigns.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                            My Campaigns
                        </a>
                        <a href="{{ route('advertiser.profile.edit') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.profile.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                            Business Profile
                        </a>
                        <a href="{{ route('advertiser.analytics.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('advertiser.analytics.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                            Performance Analytics
                        </a>
                    </nav>
                @endauth
            </div>

            <div class="flex items-center space-x-3">
                @auth('advertiser')
                    <a href="{{ route('advertiser.campaigns.create') }}" class="hidden sm:inline-flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        <span>New Ad Campaign</span>
                    </a>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="flex items-center space-x-2 text-xs font-bold px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 focus:outline-none">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-[11px]">
                                {{ strtoupper(substr(Auth::guard('advertiser')->user()->business_name, 0, 1)) }}
                            </span>
                            <span class="max-w-[140px] truncate hidden sm:inline">{{ Auth::guard('advertiser')->user()->business_name }}</span>
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;" 
                             x-cloak 
                             class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl py-2 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900 truncate">{{ Auth::guard('advertiser')->user()->business_name }}</p>
                                <p class="text-[10px] text-slate-500 truncate">{{ Auth::guard('advertiser')->user()->email }}</p>
                            </div>
                            <a href="{{ route('advertiser.profile.edit') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Edit Business Profile</a>
                            <a href="{{ route('advertiser.campaigns.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Manage Campaigns</a>
                            <a href="/" target="_blank" class="block px-4 py-2 text-slate-600 hover:bg-slate-50 border-t border-slate-100 mt-1 font-medium">View Public Site ↗</a>
                            <form action="{{ route('advertiser.logout') }}" method="POST" class="border-t border-slate-100 mt-1">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-700 font-bold hover:bg-rose-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('advertiser.login') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3 py-2">Log In</a>
                    <a href="{{ route('advertiser.register') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm">Register Business</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-2xl font-bold flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded-2xl font-bold flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-600">
        <p>&copy; {{ date('Y') }} Obituaries.co.ke Advertising Portal. All rights reserved.</p>
    </footer>

</body>
</html>
