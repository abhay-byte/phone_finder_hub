@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
            Documentation
        </h1>
        <p class="mt-4 text-xl text-gray-500 dark:text-gray-400">
            Understanding how we score and rank devices.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-10 sm:grid-cols-3">
        <!-- UEPS Card -->
        <a href="{{ route('ueps.methodology') }}" class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-teal-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300">
            <div>
                <span class="rounded-lg inline-flex p-3 bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 ring-4 ring-white dark:ring-gray-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-medium">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    UEPS-40 Methodology
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Unlock the details behind our Ultra-Extensive Phone Scoring system. 40 criteria across 7 categories.
                </p>
            </div>
            <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
                </svg>
            </span>
        </a>

        <!-- FPI Card -->
        <a href="{{ route('fpi.methodology') }}" class="group relative bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-teal-500 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300">
            <div>
                <span class="rounded-lg inline-flex p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 ring-4 ring-white dark:ring-gray-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-medium">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    Flagship Performance Index (FPI)
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    How we define and calculate "Flagship" status based on raw specs, features, and performance.
                </p>
            </div>
        </a>

        <!-- Value Calculation Card -->
        <div class="group relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300">
            <div>
                <span class="rounded-lg inline-flex p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-4 ring-white dark:ring-gray-800">
                   <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Value Calculation
                </h3>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 space-y-4">
                    <p>
                        Our <strong>Value Score</strong> is derived from a proprietary algorithm that balances a phone's capabilities against its current market price.
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 font-mono text-xs">
                        Value Score = (UEPS Score × 0.6 + FPI Score × 0.4) / Price Factor
                    </div>
                    <p>
                        We normalize prices to specific regions (Global/India) to ensure fair comparison. A higher score indicates better "Bang for Buck".
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
