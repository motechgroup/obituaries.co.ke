<!DOCTYPE html>
<html lang="en" class="h-full bg-background">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $gaMode = \App\Models\Setting::get('google_analytics_mode', 'auto');
        $gaMeasurementId = trim(\App\Models\Setting::get('google_analytics_measurement_id', ''));
        $gaScript = trim(\App\Models\Setting::get('google_analytics_script', ''));

        // Backward compatibility if script was previously pasted into measurement_id field
        if (empty($gaScript) && (str_contains(strtolower($gaMeasurementId), '<script') || str_contains($gaMeasurementId, '<'))) {
            $gaScript = $gaMeasurementId;
            $gaMeasurementId = '';
        }

        $activeGaType = null;
        if ($gaMode !== 'disabled') {
            if ($gaMode === 'script' && !empty($gaScript)) {
                $activeGaType = 'script';
            } elseif ($gaMode === 'measurement_id' && !empty($gaMeasurementId)) {
                $activeGaType = 'measurement_id';
            } elseif ($gaMode === 'auto') {
                if (!empty($gaScript)) {
                    $activeGaType = 'script';
                } elseif (!empty($gaMeasurementId)) {
                    $activeGaType = 'measurement_id';
                }
            }
        }
    @endphp

    @if($activeGaType === 'script')
        <!-- Google Analytics Custom Script -->
        {!! $gaScript !!}
    @elseif($activeGaType === 'measurement_id')
        <!-- Google tag (gtag.js) GA4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', '{{ $gaMeasurementId }}');
        </script>
    @endif
    
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

    @php
        $rawOgImage = trim($__env->yieldContent('og_image', asset('images/og-default.jpg')));
        // Force HTTPS protocol for Open Graph crawlers (Facebook, WhatsApp, Twitter)
        $ogImageUrl = preg_replace('/^http:/i', 'https:', $rawOgImage);

        $rawOgUrl = trim($__env->yieldContent('canonical_url', url()->current()));
        $ogUrl = preg_replace('/^http:/i', 'https:', $rawOgUrl);

        $ogImageWidth = trim($__env->yieldContent('og_image_width', '1200'));
        $ogImageHeight = trim($__env->yieldContent('og_image_height', '630'));
        $ogImageType = trim($__env->yieldContent('og_image_type', 'image/jpeg'));
        $ogImageAlt = trim($__env->yieldContent('og_image_alt', 'Obituaries.co.ke | Kenya Obituaries & Death Notices'));
    @endphp

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:site_name" content="Obituaries.co.ke">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:title" content="@yield('og_title', 'Obituaries.co.ke | Kenya Obituaries & Death Notices')">
    <meta property="og:description" content="@yield('og_description', 'Official Kenyan obituary publishing and memorial platform.')">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $ogImageUrl }}">
    <meta property="og:image:width" content="{{ $ogImageWidth }}">
    <meta property="og:image:height" content="{{ $ogImageHeight }}">
    <meta property="og:image:type" content="{{ $ogImageType }}">
    <meta property="og:image:alt" content="{{ $ogImageAlt }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Obituaries.co.ke | Kenya Obituaries & Death Notices')">
    <meta name="twitter:description" content="@yield('og_description', 'Official Kenyan obituary publishing and memorial platform.')">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

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
    </style>

    <!-- Performance DNS Prefetch & Preconnect Hints -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">

    <!-- Combined Non-Blocking Asynchronous Google Fonts -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=swap"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=swap" rel="stylesheet" media="print" onload="this.media='all'"/>
    
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=swap" rel="stylesheet"/>
    </noscript>

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
            <!-- Official Site Logo -->
            <a href="{{ route('home') }}" class="flex items-center flex-shrink-0 group" aria-label="Obituaries.co.ke Homepage">
                <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke Logo" width="180" height="48" class="h-10 sm:h-12 w-auto object-contain">
            </a>

            <!-- Quick Header Search (Desktop) -->
            <form action="{{ route('obituaries.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-4" role="search">
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-4 py-2 border border-outline-variant focus-within:border-primary w-full transition-all">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[20px]">search</span>
                    <input type="text" name="name" placeholder="Search obituary by name..." value="{{ request('name') }}" aria-label="Search obituary by name" class="bg-transparent border-none outline-none w-full text-sm text-on-surface placeholder-on-surface-variant/60">
                </div>
            </form>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-2" aria-label="Main Navigation">
                <a href="{{ route('obituaries.search') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Directory</a>
                <a href="{{ route('obituaries.counties') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Counties</a>
                <a href="{{ route('pages.about') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">About</a>
                <a href="{{ route('advertise') }}" class="text-xs font-bold text-amber-900 hover:text-amber-700 transition-colors py-2 px-3 min-h-[44px] inline-flex items-center space-x-1">
                    <span class="material-symbols-outlined text-[16px]">campaign</span>
                    <span>Advertise</span>
                </a>
                
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-primary-container transition-all shadow-sm flex items-center space-x-1.5 min-h-[44px]">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Submit Obituary</span>
                </a>
            </nav>

            <!-- Mobile Controls (Submit CTA + Hamburger) -->
            <div class="flex items-center gap-2 md:hidden">
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-3.5 py-2 rounded-lg text-xs font-semibold hover:bg-primary-container flex items-center space-x-1 min-h-[44px]">
                    <span class="material-symbols-outlined text-[14px]">add</span>
                    <span>Submit</span>
                </a>

                <button type="button" @click="mobileMenu = !mobileMenu" class="w-11 h-11 rounded-lg bg-surface-container flex items-center justify-center text-on-surface hover:bg-surface-container-high focus:outline-none" aria-label="Toggle Mobile Navigation Menu">
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

            <nav class="flex flex-col space-y-2 font-semibold text-sm">
                <a href="{{ route('home') }}" class="px-3.5 py-3 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2 min-h-[48px]">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Home</span>
                </a>
                <a href="{{ route('obituaries.search') }}" class="px-3.5 py-3 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2 min-h-[48px]">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>Search Directory</span>
                </a>
                <a href="{{ route('pages.about') }}" class="px-3.5 py-3 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2 min-h-[48px]">
                    <span class="material-symbols-outlined text-[18px]">info</span>
                    <span>About Us</span>
                </a>
                <a href="{{ route('pages.contact') }}" class="px-3.5 py-3 rounded-lg text-on-surface hover:bg-surface-container flex items-center space-x-2 min-h-[48px]">
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                    <span>Contact Us</span>
                </a>
                <a href="{{ route('advertise') }}" class="px-3.5 py-3 rounded-lg text-amber-900 bg-amber-50 hover:bg-amber-100 font-bold flex items-center space-x-2 min-h-[48px]">
                    <span class="material-symbols-outlined text-[18px]">campaign</span>
                    <span>Advertise With Us</span>
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
            <!-- Official Site Logo in Footer -->
            <a href="{{ route('home') }}" class="mb-4 inline-block" aria-label="Obituaries.co.ke Logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Obituaries.co.ke Logo" width="180" height="48" class="h-10 sm:h-12 w-auto object-contain">
            </a>
            <p class="font-serif text-xl sm:text-2xl font-bold text-on-surface mb-2">Remembering Lives. Sharing Memories.</p>
            <p class="text-xs text-slate-700 font-medium mb-6 sm:mb-8 max-w-md">A dignified sanctuary dedicated to preserving lasting tributes for your loved ones across Kenya.</p>
            
            <nav class="flex flex-wrap justify-center gap-2 sm:gap-4 mb-6 text-xs font-semibold">
                <a href="{{ route('home') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Home</a>
                <a href="{{ route('obituaries.search') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Search Directory</a>
                <a href="{{ route('pages.about') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">About</a>
                <a href="{{ route('pages.contact') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Contact</a>
                <a href="{{ route('advertise') }}" class="text-amber-900 font-bold hover:text-amber-700 transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Advertise With Us</a>
                <a href="{{ route('pages.privacy') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Privacy</a>
                <a href="{{ route('pages.terms') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Terms</a>
                <a href="{{ route('obituaries.submit') }}" class="text-slate-800 hover:text-primary transition-colors py-2 px-3 min-h-[44px] inline-flex items-center">Submit Obituary</a>
            </nav>

            <!-- Major County SEO Footer Links -->
            <div class="my-6 w-full bg-[#0B101D] border border-slate-800/80 rounded-2xl p-6 shadow-md text-center">
                <span class="text-[11px] uppercase font-bold tracking-[0.15em] text-amber-400 block mb-3">Obituaries by County</span>
                <div class="flex flex-wrap justify-center gap-x-2 gap-y-2 text-xs text-slate-200">
                    <a href="{{ url('/county/nairobi-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Nairobi Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/kisii-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Kisii Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/kisumu-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Kisumu Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/mombasa-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Mombasa Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/nakuru-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Nakuru Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/kiambu-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Kiambu Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/uasin-gishu-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Eldoret Obituaries</a>
                    <span class="text-slate-400 self-center">&bull;</span>
                    <a href="{{ url('/county/machakos-obituaries') }}" class="hover:text-amber-400 font-medium transition-colors py-2 px-2.5 min-h-[44px] inline-flex items-center">Machakos Obituaries</a>
                </div>
            </div>
            
            <p class="text-xs text-slate-600 font-medium">&copy; {{ date('Y') }} Obituaries.co.ke. A dignified space for remembrance.</p>
        </div>
    </footer>

</body>
</html>
