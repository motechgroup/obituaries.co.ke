<!DOCTYPE html>
<html lang="en" class="h-full bg-background">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'Obituaries.co.ke | Kenya Obituaries, Death Notices & Memorials')</title>
    <meta name="description" content="@yield('meta_description', 'Official Kenyan obituary platform. Read recent death notices, life stories, funeral schedules, and light virtual candles for loved ones across Kenya.')">
    <meta name="keywords" content="@yield('seo_keywords', 'obituary Kenya, Kenya obituaries, death notices Kenya, Kenyan obituaries, online obituary Kenya, funeral announcements Kenya, Nairobi obituaries, Kisii obituaries')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

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
<body class="bg-background font-sans text-on-surface flex flex-col min-h-screen antialiased selection:bg-secondary-container selection:text-on-secondary-container" x-data="{ mobileMenu: false }">

    <!-- Fixed Header matching Stitch Design -->
    <header class="fixed top-0 w-full z-50 bg-surface/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-outline-variant/30">
        <div class="h-20 max-w-[1200px] mx-auto px-4 sm:px-6 flex items-center justify-between gap-4">
            <!-- Logo & Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary flex items-center justify-center text-on-primary">
                    <span class="material-symbols-outlined text-[18px] sm:text-[20px]">church</span>
                </div>
                <span class="font-serif text-xl sm:text-2xl font-bold tracking-tight text-primary">Obituaries<span class="text-secondary">.co.ke</span></span>
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

                <button type="button" @click="mobileMenu = !mobileMenu" class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-on-surface hover:bg-surface-container-high focus:outline-none" aria-label="Toggle Navigation">
                    <span class="material-symbols-outlined text-[24px]" x-text="mobileMenu ? 'close' : 'menu'"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-surface border-b border-outline-variant/30 px-4 pt-3 pb-6 shadow-xl space-y-4" 
             x-cloak>
            
            <!-- Mobile Search Bar -->
            <form action="{{ route('obituaries.search') }}" method="GET" class="w-full">
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-4 py-2.5 border border-outline-variant focus-within:border-primary w-full">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[20px]">search</span>
                    <input type="text" name="name" placeholder="Search obituary by name..." value="{{ request('name') }}" class="bg-transparent border-none outline-none w-full text-sm text-on-surface placeholder-on-surface-variant/60">
                </div>
            </form>

            <nav class="flex flex-col space-y-3 font-semibold text-sm">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Home</span>
                </a>
                <a href="{{ route('obituaries.search') }}" class="px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>Search Directory</span>
                </a>
                <a href="{{ route('pages.about') }}" class="px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">info</span>
                    <span>About Us</span>
                </a>
                <a href="{{ route('pages.contact') }}" class="px-3 py-2 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                    <span>Contact Us</span>
                </a>
                <a href="{{ route('admin.login') }}" class="px-3 py-2 rounded-lg text-on-surface-variant hover:bg-surface-container flex items-center space-x-2 border-t border-outline-variant/30 pt-3">
                    <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                    <span>Admin Portal</span>
                </a>
            </nav>
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

    <!-- Footer matching Stitch Design -->
    <footer class="w-full bg-surface-container-low mt-16 sm:mt-20 border-t border-surface-container-high">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-10 sm:py-12 flex flex-col items-center text-center">
            <div class="w-10 h-[1px] bg-secondary mb-6 sm:mb-8 mx-auto"></div>
            <p class="font-serif text-xl sm:text-2xl font-bold text-on-surface mb-2">Remembering Lives. Sharing Memories.</p>
            <p class="text-xs text-on-surface-variant mb-6 sm:mb-8 max-w-md">A dignified sanctuary dedicated to preserving lasting tributes for your loved ones across Kenya.</p>
            
            <nav class="flex flex-wrap justify-center gap-4 sm:gap-8 mb-6 text-xs font-semibold">
                <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors">Home</a>
                <a href="{{ route('obituaries.search') }}" class="text-on-surface-variant hover:text-primary transition-colors">Search Directory</a>
                <a href="{{ route('pages.about') }}" class="text-on-surface-variant hover:text-primary transition-colors">About</a>
                <a href="{{ route('pages.contact') }}" class="text-on-surface-variant hover:text-primary transition-colors">Contact</a>
                <a href="{{ route('pages.privacy') }}" class="text-on-surface-variant hover:text-primary transition-colors">Privacy</a>
                <a href="{{ route('pages.terms') }}" class="text-on-surface-variant hover:text-primary transition-colors">Terms</a>
                <a href="{{ route('obituaries.submit') }}" class="text-on-surface-variant hover:text-primary transition-colors">Submit Obituary</a>
            </nav>

            <!-- Major County SEO Footer Links -->
            <div class="border-t border-outline-variant/20 pt-6 mb-8 w-full">
                <span class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant/70 block mb-3">Obituaries by County</span>
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-[11px] text-on-surface-variant/80">
                    <a href="{{ url('/county/nairobi-obituaries') }}" class="hover:text-primary font-medium">Nairobi Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/kisii-obituaries') }}" class="hover:text-primary font-medium">Kisii Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/kisumu-obituaries') }}" class="hover:text-primary font-medium">Kisumu Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/mombasa-obituaries') }}" class="hover:text-primary font-medium">Mombasa Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/nakuru-obituaries') }}" class="hover:text-primary font-medium">Nakuru Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/kiambu-obituaries') }}" class="hover:text-primary font-medium">Kiambu Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/uasin-gishu-obituaries') }}" class="hover:text-primary font-medium">Eldoret Obituaries</a>
                    <span>&bull;</span>
                    <a href="{{ url('/county/machakos-obituaries') }}" class="hover:text-primary font-medium">Machakos Obituaries</a>
                </div>
            </div>
            
            <p class="text-[11px] text-on-tertiary-container">&copy; {{ date('Y') }} Obituaries.co.ke. A dignified space for remembrance.</p>
        </div>
    </footer>

</body>
</html>
