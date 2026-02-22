<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    selectedPhones: JSON.parse(localStorage.getItem('selectedPhones') || '[]'),
    mobileMenuOpen: false,

    toggleSelection(phone) {
        const exists = this.selectedPhones.some(p => p.id === phone.id);
        if (exists) {
            this.selectedPhones = this.selectedPhones.filter(p => p.id !== phone.id);
        } else {
            if (this.selectedPhones.length >= 4) {
                alert('You can only compare up to 4 devices.');
                return;
            }
            this.selectedPhones.push(phone);
        }
        this.saveSelection();
    },

    clearSelection() {
        this.selectedPhones = [];
        this.saveSelection();
    },

    saveSelection() {
        localStorage.setItem('selectedPhones', JSON.stringify(this.selectedPhones));
    },

    compare() {
        if (this.selectedPhones.length < 2) return;
        const ids = this.selectedPhones.map(p => p.id).join(',');
        window.location.href = '/compare?ids=' + ids;
    }
}" :class="{ 'dark': darkMode }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'));
    $watch('selectedPhones', val => saveSelection());">
<script>
    // Immediate theme check to prevent FOUC
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
            '(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'PhoneFinderHub'))</title>

    @hasSection('meta')
        @yield('meta')
    @else
        <x-seo-tags />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- noUiSlider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
</head>

<body
    class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-black dark:text-gray-100 selection:bg-teal-500 selection:text-white"
    hx-indicator="#global-loader">

    {{-- Flash toast --}}
    @if (session('success') || session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-8px]" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 left-1/2 -translate-x-1/2 z-[200] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg
                {{ session('success') ? 'bg-teal-600 text-white' : 'bg-red-600 text-white' }}"
            style="min-width:260px; max-width:480px;">
            @if (session('success'))
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            @else
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
                </svg>
            @endif
            <span class="text-sm font-medium">{{ session('success') ?? session('error') }}</span>
            <button @click="show = false" class="ml-auto text-white/70 hover:text-white">&times;</button>
        </div>
    @endif
    <div class="min-h-[100dvh] flex flex-col">
        <!-- Navigation -->
        <nav x-data="{ mobileMenuOpen: false }"
            class="bg-white/70 dark:bg-black/70 backdrop-blur-2xl sticky top-0 z-50 border-b border-white/10 dark:border-white/5 shadow-lg transition-all duration-500"
            hx-boost="true" hx-target="#spa-content-wrapper" hx-select="#spa-content-wrapper"
            hx-swap="outerHTML show:window:top" hx-indicator="#global-loader">
            <!-- Animated Gradient Border -->
            <div
                class="absolute bottom-[-1px] left-0 right-0 h-[2px] bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-600 bg-[length:200%_auto] animate-gradient-x opacity-100 z-10">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center gap-3 group px-2" hx-boost="false">
                                {{-- Logo usually goes to home, safe to keep standard or boost if handled --}}
                                <div
                                    class="relative w-10 h-10 flex items-center justify-center bg-white/50 dark:bg-white/5 rounded-xl border border-white/20 dark:border-white/10 overflow-hidden transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:shadow-[0_0_20px_rgba(20,184,166,0.3)]">
                                    <img src="{{ asset('assets/logo.png') }}" alt="PhoneFinderHub Logo"
                                        class="w-7 h-7 object-contain relative z-10 transition-transform duration-500 group-hover:scale-110">
                                </div>
                                <span
                                    class="text-xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-teal-800 to-slate-900 dark:from-white dark:via-teal-400 dark:to-white bg-[length:200%_auto] animate-gradient-x hidden sm:block">
                                    PhoneFinderHub
                                </span>
                            </a>
                        </div>

                        <div class="hidden space-x-1 sm:ml-6 sm:flex items-center py-2" id="desktop-menu">
                            @if (!request()->is('admin*'))
                                <div class="flex items-center self-center mr-4">
                                    <a href="{{ route('find.index') }}"
                                        class="relative inline-flex items-center justify-center px-6 py-2.5 text-xs font-black text-white transition-all duration-500 rounded-full shadow-[0_0_15px_rgba(20,184,166,0.4)] bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:scale-105 hover:shadow-[0_0_25px_rgba(20,184,166,0.6)] focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-black group overflow-hidden">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                                        </div>
                                        <svg class="w-4 h-4 mr-2 relative z-20 group-hover:rotate-12 transition-transform duration-300"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span class="relative z-20 tracking-tighter uppercase">Find Phone</span>
                                    </a>
                                </div>
                                <a href="{{ route('home') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('home') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Home
                                </a>
                                <a href="{{ route('phones.rankings') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('phones.rankings') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Rankings
                                </a>
                                <a href="{{ route('phones.compare') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('phones.compare') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Compare
                                </a>

                                <a href="{{ route('forum.index') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('forum.*') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Forums
                                </a>
                                <a href="{{ route('blogs.index') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('blogs.*') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Blogs
                                </a>
                                <a href="{{ route('docs.index') }}"
                                    class="nav-link inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300 {{ request()->routeIs('docs.index') ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'text-slate-600 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10' }}">
                                    Docs
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- GitHub Link --}}
                        <a href="https://github.com/abhay-byte/phone_finder_hub" target="_blank"
                            rel="noopener noreferrer"
                            class="p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 dark:focus:bg-slate-800 transition duration-150 ease-in-out"
                            title="View on GitHub">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                        {{-- Theme Toggle --}}
                        <button @click="darkMode = !darkMode"
                            class="p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 dark:focus:bg-slate-800 transition duration-150 ease-in-out">
                            <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        {{-- Auth Area --}}
                        @auth
                            <div x-data="{ userMenuOpen: false }" class="relative">
                                {{-- Avatar trigger --}}
                                <button @click="userMenuOpen = !userMenuOpen"
                                    @keydown.escape.window="userMenuOpen = false"
                                    class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-black transition-all"
                                    aria-label="User menu">
                                    <div
                                        class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-white text-sm font-bold select-none">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800 py-1 z-50"
                                    style="display:none; top: calc(100% + 8px);" x-cloak>
                                    {{-- User info --}}
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ auth()->user()->username }}</p>
                                        @if (auth()->user()->isSuperAdmin())
                                            <span
                                                class="mt-1 inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide text-teal-600 dark:text-teal-400">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                Super Admin
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Admin Panel (super_admin only) --}}
                                    @if (auth()->user()->isSuperAdmin())
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-teal-600 dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/20 transition-colors font-medium">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 7a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                            </svg>
                                            Admin Panel
                                        </a>
                                    @endif
                                    {{-- Profile --}}
                                    <a href="{{ route('profile.show') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        Your Profile
                                    </a>
                                    {{-- Logout --}}
                                    <a href="{{ route('logout') }}?t={{ time() }}"
                                        class="w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Guest: Login + Sign Up --}}
                            <div class="hidden sm:flex items-center gap-2">
                                <a href="{{ route('login') }}"
                                    class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                    Sign in
                                </a>
                                <a href="{{ route('register') }}"
                                    class="text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded-lg transition-colors">
                                    Sign up
                                </a>
                            </div>
                            {{-- Mobile: icon only --}}
                            <a href="{{ route('login') }}"
                                class="sm:hidden p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </a>
                        @endauth

                        {{-- Mobile Menu Button --}}
                        <div class="flex items-center md:hidden">
                            @if (request()->is('admin*'))
                                <button @click="$dispatch('open-admin-sidebar')"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300 transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            @else
                                <button @click="mobileMenuOpen = !mobileMenuOpen"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300 transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{ 'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }"
                                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{ 'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }"
                                            class="hidden" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen"
                class="sm:hidden bg-white/95 dark:bg-black/95 backdrop-blur-xl border-t border-gray-100 dark:border-white/5"
                x-transition.origin.top x-cloak style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1" id="mobile-menu">
                    <a href="{{ route('find.index') }}"
                        class="block px-4 py-3 mx-2 mt-2 mb-3 rounded-xl text-center text-base font-extrabold text-white shadow-lg shadow-indigo-500/30 bg-gradient-to-r from-teal-500 via-indigo-500 to-purple-600 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                        <span
                            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer-skew -skew-x-12"></span>
                        <div class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-white/20">
                        </div>
                        <span class="flex items-center justify-center gap-2 relative z-10 tracking-widest uppercase">
                            <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Find Phone
                        </span>
                    </a>
                    <a href="{{ route('home') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Home
                    </a>
                    <a href="{{ route('phones.rankings') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('phones.rankings') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Rankings
                    </a>
                    <a href="{{ route('phones.compare') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('phones.compare') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Compare
                    </a>

                    <a href="{{ route('forum.index') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('forum.*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Forums
                    </a>
                    <a href="{{ route('blogs.index') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('blogs.*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Blogs
                    </a>
                    <a href="{{ route('docs.index') }}"
                        class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('docs.index') ? 'bg-teal-50 text-teal-700 dark:bg-gray-800 dark:text-teal-400' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800' }}">
                        Docs
                    </a>
                </div>
            </div>
        </nav>


        <!-- SPA Content Wrapper -->
        <div id="spa-content-wrapper" class="flex-grow flex flex-col" hx-boost="true" hx-target="#main-content"
            hx-select="#main-content" hx-swap="outerHTML show:window:top" hx-indicator="#global-loader">

            <!-- Page Content -->
            <main class="flex-grow flex flex-col" id="main-content">
                @yield('content')
            </main>

            <!-- Footer -->
            @if (!View::hasSection('hide_footer'))
                <footer
                    class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                            &copy; {{ date('Y') }} PhoneFinderHub. Data-driven decisions.
                        </p>
                    </div>
                </footer>
            @endif
        </div>
    </div>

    <!-- Floating Comparison Bar -->
    <div x-show="selectedPhones.length > 0" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 left-0 right-0 bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-white/10 shadow-lg z-50 p-4"
        style="display: none;" x-cloak>
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                    <span x-text="selectedPhones.length"></span>/4 selected
                </span>
                <div class="flex -space-x-2">
                    <template x-for="phone in selectedPhones" :key="phone.id">
                        <div class="relative group">
                            <img :src="phone.image"
                                class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 object-cover bg-white">
                            <button @click="toggleSelection(phone)"
                                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="clearSelection()"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
                    Clear
                </button>
                <button @click="compare()"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="selectedPhones.length < 2">
                    Compare
                </button>
            </div>
        </div>
    </div>

    <!-- HTMX for SPA Navigation -->
    <script src="https://unpkg.com/htmx.org@1.9.10"
        integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC" crossorigin="anonymous">
    </script>

    @stack('scripts')

    <!-- Top Progress Bar -->
    <div id="global-loader"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-white/50 dark:bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none htmx-indicator">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-teal-500"></div>
    </div>


    <!-- Global Skeleton Styles -->
    <style>
        .skeleton {
            background-color: #e5e7eb;
            /* gray-200 */
        }

        .dark .skeleton {
            background-color: #1f2937;
            /* gray-800 */
        }

        .skeleton-shimmer {
            position: relative;
            overflow: hidden;
        }

        .skeleton-shimmer::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            background-image: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0,
                    rgba(255, 255, 255, 0.2) 20%,
                    rgba(255, 255, 255, 0.5) 60%,
                    rgba(255, 255, 255, 0));
            animation: shimmer 2s infinite;
        }

        .dark .skeleton-shimmer::after {
            background-image: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0,
                    rgba(255, 255, 255, 0.05) 20%,
                    rgba(255, 255, 255, 0.1) 60%,
                    rgba(255, 255, 255, 0));
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes shimmer-skew {
            0% {
                transform: translateX(-150%) skewX(-12deg);
            }

            100% {
                transform: translateX(150%) skewX(-12deg);
            }
        }

        @keyframes gradient-x {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* Top Loader Classes */
        .loading .top-loader-active {
            opacity: 1;
        }

        .animate-gradient-x {
            animation: gradient-x 3s ease infinite;
        }

        .animate-shimmer-skew {
            animation: shimmer-skew 2s ease-in-out infinite alternate;
        }

        .group:hover .group-hover\:animate-shimmer-skew {
            animation: shimmer-skew 2s ease-in-out infinite alternate;
        }
    </style>

    <!-- noUiSlider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css"
        integrity="sha512-qveKnGrvOChbSzAdtSs8p69eoLegyh+1hwOMbmpCViIwj7rn4oJjdmMvWOuyQlTOZgTlZA0N2PXA7iA8/2TUYA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"
        integrity="sha512-UOJe4paV6hYWBnS0c9GnIRH8PLm2nFK22uhfAvsTIqd3uwnWsVri1OPn5fJYdLtGY3wB11LGHJ4yPU1WFJeBYQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const topLoader = document.getElementById('top-loader');
            const topLoaderBar = document.getElementById('top-loader-bar');

            // HTMX Events for Top Loader
            document.body.addEventListener('htmx:configRequest', () => {
                if (topLoader && topLoaderBar) {
                    topLoader.classList.remove('opacity-0');
                    topLoaderBar.style.width = '30%';
                }
            });

            document.body.addEventListener('htmx:afterOnLoad', () => {
                if (topLoader && topLoaderBar) {
                    topLoaderBar.style.width = '100%';
                    setTimeout(() => {
                        topLoader.classList.add('opacity-0');
                        topLoaderBar.style.width = '0';
                    }, 300);
                }

                // Update Navbar Active States
                const path = new URL(window.location.href).pathname;

                // Desktop
                document.querySelectorAll('#desktop-menu .nav-link').forEach(link => {
                    const href = new URL(link.href).pathname;
                    const isActive = (path === '/' && href === '/') || (href !== '/' && path
                        .startsWith(href));

                    if (isActive) {
                        link.classList.remove('text-gray-600', 'hover:text-gray-900',
                            'hover:bg-gray-100', 'dark:text-gray-400', 'dark:hover:text-white',
                            'dark:hover:bg-gray-800');
                        link.classList.add('bg-teal-50', 'text-teal-700', 'dark:bg-teal-900/30',
                            'dark:text-teal-400');
                    } else {
                        link.classList.remove('bg-teal-50', 'text-teal-700', 'dark:bg-teal-900/30',
                            'dark:text-teal-400');
                        link.classList.add('text-gray-600', 'hover:text-gray-900',
                            'hover:bg-gray-100', 'dark:text-gray-400', 'dark:hover:text-white',
                            'dark:hover:bg-gray-800');
                    }
                });

                // Mobile
                document.querySelectorAll('#mobile-menu .mobile-nav-link').forEach(link => {
                    const href = new URL(link.href).pathname;
                    const isActive = (path === '/' && href === '/') || (href !== '/' && path
                        .startsWith(href));

                    if (isActive) {
                        link.classList.remove('text-gray-700', 'hover:text-gray-900',
                            'hover:bg-gray-50', 'dark:text-gray-300', 'dark:hover:text-white',
                            'dark:hover:bg-gray-800');
                        link.classList.add('bg-teal-50', 'text-teal-700', 'dark:bg-gray-800',
                            'dark:text-teal-400');
                    } else {
                        link.classList.add('text-gray-700', 'hover:text-gray-900',
                            'hover:bg-gray-50', 'dark:text-gray-300', 'dark:hover:text-white',
                            'dark:hover:bg-gray-800');
                        link.classList.remove('bg-teal-50', 'text-teal-700', 'dark:bg-gray-800',
                            'dark:text-teal-400');
                    }
                });
            });

            // Handle browser back/forward cache
            window.addEventListener('pageshow', (event) => {
                if (event.persisted && topLoader) {
                    topLoader.classList.add('opacity-0');
                }
            });
        });
    </script>
</body>

</html>
