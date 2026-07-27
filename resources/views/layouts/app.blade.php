<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Obituaries.co.ke | Remembering Lives. Sharing Memories.')</title>
    <meta name="description" content="@yield('meta_description', 'Create and preserve a lasting tribute for your loved ones on Kenya\'s official obituary publishing platform.')">
    
    <!-- Open Graph / Meta -->
    <meta property="og:title" content="@yield('og_title', 'Obituaries.co.ke | Remembering Lives. Sharing Memories.')">
    <meta property="og:description" content="@yield('og_description', 'Create and preserve a lasting tribute for your loved ones.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .gold-border {
            border-bottom: 2px solid #D97706;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- Header Navigation -->
    <header class="bg-slate-900 text-white sticky top-0 z-50 border-b border-slate-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:bg-amber-500/20 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-serif text-xl sm:text-2xl font-bold tracking-tight text-white block">Obituaries<span class="text-amber-500">.co.ke</span></span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest block font-sans font-medium">Kenya's Tribute Platform</span>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="{{ route('home') }}" class="text-slate-300 hover:text-white transition-colors">Home</a>
                    <a href="{{ route('obituaries.search') }}" class="text-slate-300 hover:text-white transition-colors">Search Obituaries</a>
                    <a href="{{ route('pages.about') }}" class="text-slate-300 hover:text-white transition-colors">About Us</a>
                    <a href="{{ route('pages.contact') }}" class="text-slate-300 hover:text-white transition-colors">Contact</a>
                </nav>

                <!-- Action Button & Mobile Menu Toggle -->
                <div class="flex items-center space-x-4" x-data="{ open: false }">
                    <a href="{{ route('obituaries.submit') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium bg-amber-600 text-white hover:bg-amber-500 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Submit Obituary
                    </a>

                    <!-- Mobile Menu Button -->
                    <button @click="open = !open" type="button" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Toggle menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-cloak/>
                        </svg>
                    </button>

                    <!-- Mobile Dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute top-20 right-4 left-4 bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-2xl md:hidden text-base space-y-3 z-50" x-cloak>
                        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-slate-800">Home</a>
                        <a href="{{ route('obituaries.search') }}" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-slate-800">Search Obituaries</a>
                        <a href="{{ route('pages.about') }}" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-slate-800">About Us</a>
                        <a href="{{ route('pages.contact') }}" class="block px-3 py-2 rounded-lg text-slate-200 hover:bg-slate-800">Contact</a>
                        <div class="pt-2 border-t border-slate-800">
                            <a href="{{ route('admin.login') }}" class="block px-3 py-2 text-xs text-slate-400 hover:text-slate-200">Admin Portal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Flash Notification Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Peaceful & Trustworthy Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Column 1: Info -->
                <div class="md:col-span-2 space-y-4">
                    <a href="{{ route('home') }}" class="font-serif text-xl font-bold text-white block">Obituaries<span class="text-amber-500">.co.ke</span></a>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-md">
                        A dignified digital space dedicated to honoring the memories of loved ones across Kenya. Every life deserves to be remembered.
                    </p>
                    <div class="text-xs text-amber-500/90 font-medium tracking-wide">
                        KES 500 Basic Package • Fast M-Pesa STK Push • Verified Notices
                    </div>
                </div>

                <!-- Column 2: Navigation -->
                <div>
                    <h3 class="text-white font-medium mb-4 text-xs uppercase tracking-wider">Quick Links</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Home</a></li>
                        <li><a href="{{ route('obituaries.submit') }}" class="hover:text-amber-400 transition-colors">Submit Obituary</a></li>
                        <li><a href="{{ route('obituaries.search') }}" class="hover:text-amber-400 transition-colors">Search Directory</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-amber-400 transition-colors">Admin Login</a></li>
                    </ul>
                </div>

                <!-- Column 3: Legal & Support -->
                <div>
                    <h3 class="text-white font-medium mb-4 text-xs uppercase tracking-wider">Support & Legal</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('pages.about') }}" class="hover:text-amber-400 transition-colors">About Us</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-amber-400 transition-colors">Contact Support</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-amber-400 transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-amber-400 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800/80 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Obituaries.co.ke. All rights reserved.</p>
                <p class="mt-2 md:mt-0 font-serif italic text-slate-400">"Remembering Lives. Sharing Memories."</p>
            </div>
        </div>
    </footer>

</body>
</html>
