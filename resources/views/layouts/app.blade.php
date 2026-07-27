<!DOCTYPE html>
<html lang="en" class="h-full bg-background">
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

    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet"/>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background font-sans text-on-surface flex flex-col min-h-screen antialiased selection:bg-secondary-container selection:text-on-secondary-container">

    <!-- Fixed Header matching Stitch Design -->
    <header class="fixed top-0 w-full z-50 bg-surface/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-outline-variant/30">
        <div class="h-20 max-w-[1200px] mx-auto px-6 flex items-center justify-between gap-6">
            <!-- Logo & Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0 group">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-on-primary">
                    <span class="material-symbols-outlined text-[20px]">church</span>
                </div>
                <span class="font-serif text-2xl font-bold tracking-tight text-primary">Obituaries<span class="text-secondary">.co.ke</span></span>
            </a>

            <!-- Quick Header Search -->
            <form action="{{ route('obituaries.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-4 py-2 border border-outline-variant focus-within:border-primary w-full transition-all">
                    <span class="material-symbols-outlined text-on-surface-variant mr-2 text-[20px]">search</span>
                    <input type="text" name="name" placeholder="Search obituary by name..." value="{{ request('name') }}" class="bg-transparent border-none outline-none w-full text-sm text-on-surface placeholder-on-surface-variant/60">
                </div>
            </form>

            <!-- Nav Links -->
            <nav class="flex items-center gap-4 sm:gap-6">
                <a href="{{ route('obituaries.search') }}" class="hidden sm:inline-block text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">Directory</a>
                <a href="{{ route('pages.about') }}" class="hidden lg:inline-block text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">About</a>
                <a href="{{ route('admin.login') }}" class="text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors">Admin Portal</a>
                
                <a href="{{ route('obituaries.submit') }}" class="bg-primary text-on-primary px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-primary-container transition-all shadow-sm flex items-center space-x-1.5">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Submit Obituary</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Global Flash Notification Banner -->
    <div class="pt-24 max-w-[1200px] w-full mx-auto px-6">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3.5 rounded-xl text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3.5 rounded-xl text-sm flex items-center justify-between shadow-xs mb-4">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined text-rose-600">error</span>
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
    <footer class="w-full bg-surface-container-low mt-20 border-t border-surface-container-high">
        <div class="max-w-[1200px] mx-auto px-6 py-12 flex flex-col items-center text-center">
            <div class="w-10 h-[1px] bg-secondary mb-8 mx-auto"></div>
            <p class="font-serif text-2xl font-bold text-on-surface mb-2">Remembering Lives. Sharing Memories.</p>
            <p class="text-xs text-on-surface-variant mb-8 max-w-md">A dignified sanctuary dedicated to preserving lasting tributes for your loved ones across Kenya.</p>
            
            <nav class="flex flex-wrap justify-center gap-8 mb-10 text-xs font-semibold">
                <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors">Home</a>
                <a href="{{ route('obituaries.search') }}" class="text-on-surface-variant hover:text-primary transition-colors">Search Directory</a>
                <a href="{{ route('pages.about') }}" class="text-on-surface-variant hover:text-primary transition-colors">About</a>
                <a href="{{ route('pages.contact') }}" class="text-on-surface-variant hover:text-primary transition-colors">Contact</a>
                <a href="{{ route('pages.privacy') }}" class="text-on-surface-variant hover:text-primary transition-colors">Privacy</a>
                <a href="{{ route('pages.terms') }}" class="text-on-surface-variant hover:text-primary transition-colors">Terms</a>
                <a href="{{ route('obituaries.submit') }}" class="text-on-surface-variant hover:text-primary transition-colors">Submit Obituary</a>
            </nav>
            
            <p class="text-[11px] text-on-tertiary-container">&copy; {{ date('Y') }} Obituaries.co.ke. A dignified space for remembrance.</p>
        </div>
    </footer>

</body>
</html>
