@extends('layouts.app')

@section('content')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12 animate-fadeInUp">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                Documentation
            </h1>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">
                Understanding how we score and rank devices.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-10 sm:grid-cols-3">
            <!-- UEPS Card -->
            <a href="{{ route('ueps.methodology') }}"
                class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-teal-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 animate-fadeInUp delay-100">
                <div>
                    <span
                        class="rounded-lg inline-flex p-3 bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 ring-4 ring-white dark:ring-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-medium">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        UEPS-45 Methodology
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Unlock the details behind our Ultra-Extensive Phone Scoring system. 40 criteria across 7 categories.
                    </p>
                </div>
                <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
                    aria-hidden="true">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
                    </svg>
                </span>
            </a>

            <!-- FPI Card -->
            <a href="{{ route('fpi.methodology') }}"
                class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-teal-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 animate-fadeInUp delay-200">
                <div>
                    <span
                        class="rounded-lg inline-flex p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 ring-4 ring-white dark:ring-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-medium">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        Final Performance Index (FPI)
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        How we define and calculate "Final" status based on raw specs, features, and performance.
                    </p>
                </div>
            </a>

            <!-- GPX Card -->
            <a href="{{ route('docs.gpx') }}"
                class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-teal-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 animate-fadeInUp delay-200">
                <div>
                    <span
                        class="rounded-lg inline-flex p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 ring-4 ring-white dark:ring-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        GPX-300 Gaming Index
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        The new standard for competitive mobile gaming. 300-point system covering thermals, emulation, and
                        input latency.
                    </p>
                </div>
            </a>

            <!-- CMS-1000 Card -->
            <a href="{{ route('cms.methodology') }}"
                class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-purple-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 animate-fadeInUp delay-300">
                <div>
                    <span
                        class="rounded-lg inline-flex p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 ring-4 ring-white dark:ring-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        CMS-1000 Camera Mastery
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Comprehensive 1000-point camera scoring system evaluating hardware specs, imaging capabilities, and
                        professional benchmarks.
                    </p>
                </div>
            </a>

            <!-- Value Calculation Card -->
            <div
                class="group relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 animate-fadeInUp delay-300">
                <div>
                    <span
                        class="rounded-lg inline-flex p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-4 ring-white dark:ring-gray-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Value Calculation
                    </h3>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 space-y-4">
                        <p>
                            Our <strong>Value Score</strong> is derived from a proprietary algorithm that balances a phone's
                            capabilities against its current market price.
                        </p>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 font-mono text-xs">
                            Value Score = (UEPS Score × 0.6 + FPI Score × 0.4) / Price Factor
                        </div>
                        <p>
                            We normalize prices to specific regions (Global/India) to ensure fair comparison. A higher score
                            indicates better "Bang for Buck".
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
