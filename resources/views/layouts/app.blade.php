<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Phone Finder') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-black dark:text-gray-100 selection:bg-teal-500 selection:text-white">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/80 dark:bg-black/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 dark:border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                                <div class="relative w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden transition-transform duration-300 group-hover:scale-105 group-hover:shadow-md">
                                    <img src="{{ asset('assets/logo.png') }}" alt="PhoneFinderHub Logo" class="w-8 h-8 object-contain relative z-10">
                                </div>
                                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 group-hover:to-indigo-500 transition-all duration-300 hidden sm:block">
                                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 group-hover:to-teal-500 transition-all duration-300 hidden sm:block">
                                    PhoneFinderHub
                                </span>
                            </a>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                             <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-teal-700 transition duration-150 ease-in-out">
                                Home
                            </a>
                            <a href="{{ route('phones.rankings') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Rankings
                            </a>
                            <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Compare
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <!-- Theme Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800 dark:focus:bg-slate-800 transition duration-150 ease-in-out">
                            <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow" id="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} PhoneFinderHub. Data-driven decisions.
                </p>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
