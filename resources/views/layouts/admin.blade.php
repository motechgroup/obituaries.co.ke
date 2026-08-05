<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard | Obituaries.co.ke')</title>

    <!-- Favicon & Site Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .obituary-biography p {
            margin-bottom: 1.5rem !important;
            line-height: 1.85 !important;
        }
        .obituary-biography p:last-child {
            margin-bottom: 0 !important;
        }
        .obituary-biography h1, .obituary-biography h2, .obituary-biography h3, .obituary-biography h4 {
            font-family: Georgia, serif;
            font-weight: 700;
            color: #0f172a;
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
    </style>
    <script>
        window.applyTag = function(tag, elementId = 'admin_biography') {
            const el = document.getElementById(elementId) || document.querySelector('textarea[name="biography"]');
            if (!el) return;
            const start = el.selectionStart || 0;
            const end = el.selectionEnd || 0;
            const selectedText = el.value.substring(start, end) || 'text';
            const replacement = `<${tag}>${selectedText}</${tag}>`;
            el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
            el.selectionStart = start;
            el.selectionEnd = start + replacement.length;
            el.focus();
            el.dispatchEvent(new Event('input', { bubbles: true }));
        };

        window.applyList = function(elementId = 'admin_biography') {
            const el = document.getElementById(elementId) || document.querySelector('textarea[name="biography"]');
            if (!el) return;
            const start = el.selectionStart || 0;
            const end = el.selectionEnd || 0;
            const selectedText = el.value.substring(start, end) || 'Item 1';
            const replacement = `<ul>\n  <li>${selectedText}</li>\n</ul>`;
            el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
            el.selectionStart = start;
            el.selectionEnd = start + replacement.length;
            el.focus();
            el.dispatchEvent(new Event('input', { bubbles: true }));
        };
    </script>
</head>
<body class="h-full bg-slate-100 antialiased font-sans text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Mobile Header Bar -->
        <div class="md:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between border-b border-slate-800 sticky top-0 z-40">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo-light.svg') }}" alt="Obituaries.co.ke Admin" class="h-8 w-auto object-contain">
                <span class="bg-amber-500/20 text-amber-400 text-[10px] uppercase font-sans font-semibold px-2 py-0.5 rounded border border-amber-500/30">Admin</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" type="button" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Sidebar Overlay for Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-xs md:hidden" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- Sidebar Navigation Drawer -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed md:sticky top-0 inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 transition-transform duration-200 ease-in-out h-screen flex-shrink-0">
            
            <!-- Top Section: Brand & Nav Links -->
            <div class="flex flex-col flex-grow overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800/80 flex-shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('images/logo-light.svg') }}" alt="Obituaries.co.ke Admin" class="h-10 w-auto object-contain">
                    </a>
                </div>

                <!-- Navigation List -->
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Obituaries Directory Dropdown / Group -->
                    <div x-data="{ open: true }">
                        <div class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.obituaries.*') ? 'bg-slate-800 text-white font-semibold border border-slate-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <a href="{{ route('admin.obituaries.index') }}" class="flex items-center space-x-3 flex-grow">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.obituaries.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"/>
                                </svg>
                                <span>Obituaries Directory</span>
                            </a>
                            <div class="flex items-center space-x-2">
                                @php
                                    $pendingVerCount = \App\Models\Obituary::where('status', 'pending_verification')->count();
                                @endphp
                                @if($pendingVerCount > 0)
                                    <span class="text-xs bg-amber-500/20 text-amber-400 font-bold px-2 py-0.5 rounded-full border border-amber-500/30">
                                        {{ $pendingVerCount }}
                                    </span>
                                @endif
                                <button type="button" @click="open = !open" class="text-slate-400 hover:text-white p-1 focus:outline-none">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Sub-menu Filters with Live Counters -->
                        <div x-show="open" class="pl-9 pr-2 py-1.5 space-y-1 text-xs font-normal">
                            <a href="{{ route('admin.obituaries.create') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors text-amber-400 font-bold hover:bg-slate-800/80">
                                <span>+ Add New Obituary</span>
                                @if(Auth::guard('admin')->user()->isSuperAdmin())
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30">Free</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30">M-Pesa</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'pending_verification' ? 'text-amber-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Pending Verification</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-mono bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    {{ \App\Models\Obituary::where('status', 'pending_verification')->count() }}
                                </span>
                            </a>
                            <a href="{{ route('admin.obituaries.index', ['status' => 'published']) }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'published' ? 'text-emerald-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Published Notices</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ \App\Models\Obituary::where('status', 'published')->count() }}
                                </span>
                            </a>
                            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'pending_payment' ? 'text-slate-200 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Pending Payment</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-mono bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ \App\Models\Obituary::where('status', 'pending_payment')->count() }}
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Obituary Reports -->
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                            <span>Obituary Reports</span>
                        </div>
                        @php
                            $pendingReportsCount = \App\Models\ObituaryReport::where('status', 'pending')->count();
                        @endphp
                        @if($pendingReportsCount > 0)
                            <span class="text-xs bg-rose-500/20 text-rose-400 font-bold px-2 py-0.5 rounded-full border border-rose-500/30">
                                {{ $pendingReportsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Advertising Management Module Dropdown -->
                    <div x-data="{ open: {{ request()->routeIs('admin.advertising.*') ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.advertising.*') ? 'bg-slate-800 text-white font-semibold border border-slate-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <a href="{{ route('admin.advertising.campaigns.index') }}" class="flex items-center space-x-3 flex-grow">
                                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.advertising.*') ? 'text-amber-400' : 'text-slate-400' }}">campaign</span>
                                <span>Advertising System</span>
                            </a>
                            <div class="flex items-center space-x-2">
                                @php
                                    $pendingAdCount = 0;
                                    try {
                                        if (!\Illuminate\Support\Facades\Schema::hasTable('ad_campaigns')) {
                                            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                                        }
                                        if (\Illuminate\Support\Facades\Schema::hasTable('ad_campaigns')) {
                                            $pendingAdCount = \App\Models\AdCampaign::where('status', 'pending_approval')->count();
                                        }
                                    } catch (\Throwable $e) {}
                                @endphp
                                @if($pendingAdCount > 0)
                                    <span class="text-xs bg-amber-500/20 text-amber-400 font-bold px-2 py-0.5 rounded-full border border-amber-500/30">
                                        {{ $pendingAdCount }}
                                    </span>
                                @endif
                                <button type="button" @click="open = !open" class="text-slate-400 hover:text-white p-1 focus:outline-none">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Advertising Submenu Links -->
                        <div x-show="open" class="pl-9 pr-2 py-1.5 space-y-1 text-xs font-normal">
                            <a href="{{ route('admin.advertising.campaigns.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.campaigns.*') ? 'text-amber-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Ad Campaigns</span>
                            </a>
                            <a href="{{ route('admin.advertising.finance.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.finance.*') ? 'text-emerald-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Financial Revenue</span>
                            </a>
                            <a href="{{ route('admin.advertising.advertisers.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.advertisers.*') ? 'text-sky-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Advertisers Directory</span>
                            </a>
                            <a href="{{ route('admin.advertising.pricing.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.pricing.*') ? 'text-slate-200 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Pricing Matrix</span>
                            </a>
                            <a href="{{ route('admin.advertising.placements.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.placements.*') ? 'text-slate-200 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Ad Placements</span>
                            </a>
                            <a href="{{ route('admin.advertising.categories.index') }}" class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.advertising.categories.*') ? 'text-slate-200 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                <span>&bull; Business Categories</span>
                            </a>
                        </div>
                    </div>

                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                        <!-- Public Contributors Directory -->
                        <a href="{{ route('admin.contributors.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.contributors.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.contributors.*') ? 'text-white' : 'text-slate-400' }}">groups</span>
                            <span>Submitters & Contributors</span>
                        </a>

                        <!-- Security Audit Logs -->
                        <a href="{{ route('admin.security-logs.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.security-logs.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.security-logs.*') ? 'text-white' : 'text-slate-400' }}">verified_user</span>
                            <span>Security Audit Logs</span>
                        </a>

                        <!-- Fraud & Threat Monitoring Center -->
                        <a href="{{ route('admin.fraud.index') }}" class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.fraud.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.fraud.*') ? 'text-white' : 'text-slate-400' }}">security</span>
                                <span>Fraud Threat Center</span>
                            </div>
                            @php
                                $openFraudCount = \App\Models\FraudAlert::where('status', 'open')->count();
                            @endphp
                            @if($openFraudCount > 0)
                                <span class="text-xs bg-amber-500 text-slate-950 font-black px-2 py-0.5 rounded-full border border-amber-400">
                                    {{ $openFraudCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Traffic Analytics -->
                        <a href="{{ route('admin.analytics.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.analytics.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.analytics.*') ? 'text-white' : 'text-slate-400' }}">analytics</span>
                            <span>Traffic Analytics</span>
                        </a>

                        <!-- M-Pesa Payments -->
                        <a href="{{ route('admin.payments.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.payments.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.payments.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>M-Pesa Payments Log</span>
                        </a>

                        <!-- Staff Accounts & Roles -->
                        <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Staff Accounts & Roles</span>
                        </a>
                    @endif

                    <!-- My Profile -->
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.profile.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.profile.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>My Profile</span>
                    </a>

                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                        <!-- Platform Settings -->
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Platform Settings</span>
                        </a>
                    @endif

                    <div class="pt-4 pb-1">
                        <div class="border-t border-slate-800"></div>
                    </div>

                    <!-- External Website Link -->
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 text-xs text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-colors">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <span>Public Website</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </nav>
            </div>

            <!-- Bottom Section: Admin User Footer & Logout -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center space-x-3 overflow-hidden group flex-grow pr-2" title="Manage Admin Profile">
                        <div class="w-9 h-9 rounded-full bg-amber-600 text-white font-bold flex items-center justify-center text-sm flex-shrink-0 shadow-sm overflow-hidden border border-amber-400/30 group-hover:border-amber-400 transition-all">
                            @if(Auth::guard('admin')->user()->avatar ?? false)
                                <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" alt="{{ Auth::guard('admin')->user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>
                        <div class="truncate text-xs">
                            <span class="font-bold text-white block truncate group-hover:text-amber-400 transition-colors">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</span>
                            <span class="text-slate-400 block truncate text-[11px]">Edit Profile &rarr;</span>
                        </div>
                    </a>

                    <form action="{{ route('admin.logout') }}" method="POST" class="inline flex-shrink-0">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body Area -->
        <div class="flex-grow flex flex-col min-w-0">
            
            <!-- Global Flash Banner -->
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </main>

            <!-- Admin Footer -->
            <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
                Obituaries.co.ke Verification & Administration Engine &copy; {{ date('Y') }}
            </footer>
        </div>

    </div>

</body>
</html>
