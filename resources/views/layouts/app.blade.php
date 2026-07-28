<!DOCTYPE html>
<html lang="en" class="h-full bg-background">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'Obituaries.co.ke | Kenya Obituaries, Death Notices & Memorials')</title>
    <meta name="description" content="@yield('meta_description', 'Official Kenyan obituary platform. Read recent death notices, life stories, funeral schedules, and light virtual candles for loved ones across Kenya.')">
    <meta name="keywords" content="@yield('seo_keywords', 'obituary Kenya, Kenya obituaries, death notices Kenya, Kenyan obituaries, online obituary Kenya, funeral announcements Kenya, Nairobi obituaries, Kisii obituaries')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Favicon & Site Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    @hasSection('noindex')
        <meta name="robots" content="noindex, nofollow">
    @endif

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:site_name" content="Obituaries.co.ke">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Obituaries.co.ke | Kenya Obituaries & Death Notices')">
    <meta property="og:description" content="@yield('og_description', 'Official Kenyan obituary publishing and memorial platform.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Obituaries.co.ke | Kenya Obituaries & Death Notices')">
    <meta name="twitter:description" content="@yield('og_description', 'Official Kenyan obituary publishing and memorial platform.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .obituary-biography p {
            margin-bottom: 1.5rem !important;
            line-height: 1.85 !important;
        }
        .obituary-biography p:last-child {
            margin-bottom: 0 !important;
        }
        .obituary-biography h1, .obituary-biography h2, .obituary-biography h3, .obituary-biography h4 {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            color: #000a1e;
            margin-top: 1.75rem !important;
            margin-bottom: 0.75rem !important;
            line-height: 1.35 !important;
        }
        .obituary-biography ul, .obituary-biography ol {
            margin-top: 1rem !important;
            margin-bottom: 1.5rem !important;
            padding-left: 1.75rem !important;
        }
        .obituary-biography ul { list-style-type: disc !important; }
        .obituary-biography ol { list-style-type: decimal !important; }
        .obituary-biography li { margin-bottom: 0.5rem !important; }
        .obituary-biography blockquote {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            padding-left: 1.25rem !important;
            border-left: 4px solid #775a19;
            font-style: italic;
            color: #44474e;
        }
        [x-cloak] { display: none !important; }
        @media (min-width: 768px) {
            .mobile-drawer-menu {
                display: none !important;
            }
        }
    </style>

    @php
        $gaId = trim(\App\Models\Setting::get('google_analytics_measurement_id', ''));
    @endphp

    @if(!empty($gaId))
        @if(str_contains($gaId, '<script'))
            {!! $gaId !!}
        @else
            <!-- Google tag (gtag.js) GA4 -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '{{ $gaId }}');
            </script>
        @endif
    @endif

    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet"/>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Schema.org Organization & WebSite JSON-LD -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "{{ url('/') }}#organization",
          "name": "Obituaries.co.ke",
          "url": "{{ url('/') }}",
          "logo": "{{ asset('images/logo.png') }}",
          "description": "Kenya's official online obituary and death notice publishing platform.",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nairobi",
            "addressCountry": "KE"
          },
          "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Support",
            "email": "support@obituaries.co.ke"
          }
        },
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}#website",
          "url": "{{ url('/') }}",
          "name": "Obituaries.co.ke",
          "publisher": {
            "@id": "{{ url('/') }}#organization"
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/search') }}?name={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    @yield('structured_data')
</head>
<body class="bg-background font-sans text-on-surface flex flex-col min-h-screen antialiased selection:bg-secondary-container selection:text-on-secondary-container" x-data="{ mobileMenu: false }" @resize.window="if (window.innerWidth >= 768) mobileMenu = false">

    <!-- Fixed Header matching Stitch Design -->
    <header class="fixed top-0 w-full z-50 bg-surface/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-outline-variant/30">
        <div class="h-20 max-w-[1200px] mx-auto px-4 sm:px-6 flex items-center justify-between gap-4">
            <!-- Official Site Logo -->
            <a href="{{ route('home') }}" class="flex items-center flex-shrink-0 group">
                <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke Logo" class="h-10 sm:h-12 w-auto object-contain">
            </a>

            <!-- Quick Header Search (Desktop) -->
            <form action="{{ route('obituaries.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-4 py-2 border border-outline-variant focus-within:border-primary w-full transition-all">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[20px]">search</span>
                    <input type="text" name="name" placeholder="Search obituary by name..." value="{{ request('name') }}" class="bg-transparent border-none outline-none w-full text-sm text-on-surface placeholder-on-surface-variant/60">
                </div>
            </form>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('obituaries.search') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">Directory</a>
                <a href="{{ url('/county/nairobi-obituaries') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">Counties</a>
                <a href="{{ route('pages.about') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">About</a>
                <a href="{{ route('admin.login') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">Admin Portal</a>
                
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-primary-container transition-all shadow-sm flex items-center space-x-1.5">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Submit Obituary</span>
                </a>
            </nav>

            <!-- Mobile Controls (Submit CTA + Hamburger) -->
            <div class="flex items-center gap-2 md:hidden">
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-3.5 py-2 rounded-lg text-xs font-semibold hover:bg-primary-container flex items-center space-x-1">
                    <span class="material-symbols-outlined text-[14px]">add</span>
                    <span>Submit</span>
                </a>

                <button type="button" @click="mobileMenu = !mobileMenu" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-all border border-slate-700/80 focus:outline-none shadow-xs" aria-label="Toggle Navigation">
                    <svg x-show="!mobileMenu" class="w-6 h-6 text-slate-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenu" class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation Overlay -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="mobileMenu = false"
             class="mobile-drawer-menu md:hidden fixed left-0 right-0 top-[72px] sm:top-[80px] bottom-0 z-[999] w-full text-white overflow-y-auto px-5 py-6 flex flex-col justify-between shadow-2xl border-t border-slate-800" 
             style="background-color: #0b0e18;"
             x-cloak>
            
            <div class="space-y-6">
                <!-- Mobile Search Bar -->
                <form action="{{ route('obituaries.search') }}" method="GET" class="w-full">
                    <div class="relative flex items-center bg-slate-900 rounded-xl px-4 py-3 border border-slate-700/80 focus-within:border-amber-500 w-full shadow-inner">
                        <span class="material-symbols-outlined text-amber-400 mr-2 text-[20px]">search</span>
                        <input type="text" name="name" placeholder="Search obituary by name..." value="{{ request('name') }}" class="bg-transparent border-none outline-none w-full text-sm text-white placeholder-slate-400">
                    </div>
                </form>

                <nav class="flex flex-col space-y-2.5 font-semibold text-sm">
                    <a href="{{ route('home') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-100 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-amber-400 text-[20px]">home</span>
                        <span>Home</span>
                    </a>
                    <a href="{{ route('obituaries.search') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-100 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-amber-400 text-[20px]">menu_book</span>
                        <span>Search Directory</span>
                    </a>
                    <a href="{{ url('/county/nairobi-obituaries') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-100 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-amber-400 text-[20px]">location_on</span>
                        <span>Browse Counties</span>
                    </a>
                    <a href="{{ route('pages.about') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-100 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-amber-400 text-[20px]">info</span>
                        <span>About Us</span>
                    </a>
                    <a href="{{ route('pages.contact') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-100 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-amber-400 text-[20px]">mail</span>
                        <span>Contact Us</span>
                    </a>
                    <a href="{{ route('admin.login') }}" @click="mobileMenu = false" class="px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800/80 hover:text-amber-400 flex items-center space-x-3 transition-colors border border-slate-800/50">
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">admin_panel_settings</span>
                        <span>Admin Portal</span>
                    </a>
                </nav>
            </div>

            <!-- Mobile Drawer Bottom CTA -->
            <div class="pt-6 border-t border-slate-800/80 mt-6">
                <a href="{{ route('obituaries.submit') }}" @click="mobileMenu = false" class="w-full bg-[#FF9800] hover:bg-[#FFA726] text-black font-extrabold px-6 py-3.5 rounded-xl text-sm transition-all shadow-lg flex items-center justify-center space-x-2">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    <span>Submit Obituary Notice</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Global Flash Notification Banner -->
    <div class="pt-24 max-w-[1200px] w-full mx-auto px-4 sm:px-6">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="flex-grow w-full">
        @yield('content')
    </main>

    <!-- Footer with Rich Dark Background & Amber Accents -->
    <footer class="w-full text-white mt-16 sm:mt-20 border-t border-slate-800/80 relative overflow-hidden" style="background-color: #0b0e18;">
        <!-- Background Subtle Glow Accent -->
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[800px] h-[250px] bg-amber-500/5 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-12 sm:py-16 flex flex-col items-center text-center relative z-10">
            <!-- Official Site Logo in Footer -->
            <a href="{{ route('home') }}" class="mb-4 inline-block group transition-transform hover:scale-105">
                <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke Logo" class="h-10 sm:h-12 w-auto object-contain brightness-0 invert filter">
            </a>
            <p class="font-serif text-xl sm:text-2xl font-bold text-white mb-2">Remembering Lives. Sharing Memories.</p>
            <p class="text-xs sm:text-sm text-slate-400 mb-6 sm:mb-8 max-w-md">A dignified sanctuary dedicated to preserving lasting tributes for your loved ones across Kenya.</p>
            
            <nav class="flex flex-wrap justify-center gap-4 sm:gap-8 mb-8 text-xs sm:text-sm font-semibold">
                <a href="{{ route('home') }}" class="text-slate-300 hover:text-amber-400 transition-colors">Home</a>
                <a href="{{ route('obituaries.search') }}" class="text-slate-300 hover:text-amber-400 transition-colors">Search Directory</a>
                <a href="{{ route('pages.about') }}" class="text-slate-300 hover:text-amber-400 transition-colors">About</a>
                <a href="{{ route('pages.contact') }}" class="text-slate-300 hover:text-amber-400 transition-colors">Contact</a>
                <a href="{{ route('pages.privacy') }}" class="text-slate-300 hover:text-amber-400 transition-colors">Privacy</a>
                <a href="{{ route('pages.terms') }}" class="text-slate-300 hover:text-amber-400 transition-colors">Terms</a>
                <a href="{{ route('obituaries.submit') }}" class="text-amber-400 hover:text-amber-300 transition-colors font-bold">Submit Obituary</a>
            </nav>

            <!-- Major County SEO Footer Links -->
            <div class="border-t border-slate-800/80 pt-6 mb-8 w-full">
                <span class="text-[10px] uppercase font-bold tracking-widest text-amber-400/90 block mb-3">Obituaries by County</span>
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-[11px] sm:text-xs text-slate-400">
                    <a href="{{ url('/county/nairobi-obituaries') }}" class="hover:text-amber-400 transition-colors">Nairobi Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/kisii-obituaries') }}" class="hover:text-amber-400 transition-colors">Kisii Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/kisumu-obituaries') }}" class="hover:text-amber-400 transition-colors">Kisumu Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/mombasa-obituaries') }}" class="hover:text-amber-400 transition-colors">Mombasa Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/nakuru-obituaries') }}" class="hover:text-amber-400 transition-colors">Nakuru Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/kiambu-obituaries') }}" class="hover:text-amber-400 transition-colors">Kiambu Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/uasin-gishu-obituaries') }}" class="hover:text-amber-400 transition-colors">Eldoret Obituaries</a>
                    <span class="text-slate-600">&bull;</span>
                    <a href="{{ url('/county/machakos-obituaries') }}" class="hover:text-amber-400 transition-colors">Machakos Obituaries</a>
                </div>
            </div>
            
            <p class="text-[11px] sm:text-xs text-slate-500">&copy; {{ date('Y') }} Obituaries.co.ke. A dignified space for remembrance.</p>
        </div>
    </footer>

</body>
</html>
