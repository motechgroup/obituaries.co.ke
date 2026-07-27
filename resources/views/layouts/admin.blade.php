<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard | Obituaries.co.ke')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-slate-100 antialiased font-sans text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Mobile Header Bar -->
        <div class="md:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between border-b border-slate-800 sticky top-0 z-40">
            <a href="{{ route('admin.dashboard') }}" class="font-serif text-lg font-bold text-white flex items-center space-x-2">
                <span>Obituaries<span class="text-amber-500">.co.ke</span></span>
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
                <div class="h-20 px-6 flex items-center border-b border-slate-800/80 flex-shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-serif text-lg font-bold text-white block tracking-tight">Obituaries<span class="text-amber-500">.co.ke</span></span>
                            <span class="text-[10px] text-amber-400 uppercase tracking-widest block font-sans font-semibold">Admin Portal</span>
                        </div>
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
                        <a href="{{ route('admin.obituaries.index') }}" class="w-full flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.obituaries.*') ? 'bg-slate-800 text-white font-semibold border border-slate-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.obituaries.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"/>
                                </svg>
                                <span>Obituaries Directory</span>
                            </div>
                            <span class="text-xs bg-amber-500/20 text-amber-400 font-bold px-2 py-0.5 rounded-full border border-amber-500/30">
                                {{ \App\Models\Obituary::where('status', 'pending_verification')->count() }}
                            </span>
                        </a>

                        <!-- Sub-menu Filters -->
                        <div class="pl-11 pr-2 py-1.5 space-y-1 text-xs font-normal">
                            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_verification']) }}" class="block py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'pending_verification' ? 'text-amber-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                &bull; Pending Verification
                            </a>
                            <a href="{{ route('admin.obituaries.index', ['status' => 'published']) }}" class="block py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'published' ? 'text-emerald-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                &bull; Published Notices
                            </a>
                            <a href="{{ route('admin.obituaries.index', ['status' => 'pending_payment']) }}" class="block py-1.5 px-2.5 rounded-lg transition-colors {{ request('status') === 'pending_payment' ? 'text-slate-200 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-slate-200' }}">
                                &bull; Pending Payment
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

                    <!-- Platform Settings -->
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-amber-600 text-white font-semibold shadow-md shadow-amber-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Platform Settings</span>
                    </a>

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
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-amber-600 text-white font-bold flex items-center justify-center text-sm flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate text-xs">
                            <span class="font-bold text-white block truncate">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</span>
                            <span class="text-slate-400 block truncate text-[11px]">{{ Auth::guard('admin')->user()->email ?? 'admin@obituaries.co.ke' }}</span>
                        </div>
                    </div>

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
