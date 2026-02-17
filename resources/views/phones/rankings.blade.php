@extends('layouts.app')

@section('content')
    <div
        class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-teal-500 selection:text-white animate-fadeInUp">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Smartphone Rankings
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                @if($tab == 'overall')
                <!-- Sidebar (Only on Expert Tab) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Filter Card -->
                    <div class="bg-white dark:bg-[#121212] rounded-2xl shadow-sm border border-slate-200 dark:border-white/5 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Filters</h3>
                            <button id="reset-filters" class="text-xs text-slate-500 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-400 font-medium transition-colors">
                                Reset
                            </button>
                        </div>
                        
                        <!-- ToolCool CDN -->
                        <script src="https://cdn.jsdelivr.net/npm/toolcool-range-slider/dist/toolcool-range-slider.min.js"></script>

                        <!-- Price Range -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Price Range</label>
                            <tc-range-slider
                                id="price-slider"
                                min="0"
                                max="{{ $maxDatabasePrice ?? 200000 }}"
                                step="1000"
                                value1="{{ $minPrice ?? 0 }}"
                                value2="{{ $maxPrice ?? $maxDatabasePrice ?? 200000 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="price-min-display">₹{{ number_format($minPrice ?? 0) }}</span>
                                <span id="price-max-display">₹{{ number_format($maxPrice ?? $maxDatabasePrice ?? 200000) }}</span>
                            </div>
                            <input type="hidden" id="min_price" name="min_price" value="{{ $minPrice ?? 0 }}">
                            <input type="hidden" id="max_price" name="max_price" value="{{ $maxPrice ?? $maxDatabasePrice ?? 200000 }}">
                        </div>

                        <!-- RAM Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">RAM (GB)</label>
                            <tc-range-slider
                                id="ram-slider"
                                min="2"
                                max="24"
                                step="1"
                                value1="{{ $minRam ?? 4 }}"
                                value2="{{ $maxRam ?? 24 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="ram-min-display">{{ $minRam ?? 4 }} GB</span>
                                <span id="ram-max-display">{{ $maxRam ?? 24 }} GB</span>
                            </div>
                            <input type="hidden" id="min_ram" name="min_ram" value="{{ $minRam ?? 4 }}">
                            <input type="hidden" id="max_ram" name="max_ram" value="{{ $maxRam ?? 24 }}">
                        </div>

                        <!-- Storage Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Storage</label>
                            <tc-range-slider
                                id="storage-slider"
                                min="32"
                                max="1024"
                                step="32"
                                value1="{{ $minStorage ?? 64 }}"
                                value2="{{ $maxStorage ?? 1024 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="storage-min-display">{{ $minStorage < 1000 ? $minStorage . ' GB' : ($minStorage/1024) . ' TB' }}</span>
                                <span id="storage-max-display">{{ $maxStorage < 1000 ? $maxStorage . ' GB' : ($maxStorage/1024) . ' TB' }}</span>
                            </div>
                            <input type="hidden" id="min_storage" name="min_storage" value="{{ $minStorage ?? 64 }}">
                            <input type="hidden" id="max_storage" name="max_storage" value="{{ $maxStorage ?? 1024 }}">
                        </div>

                        <!-- Enthusiast Filters -->
                        <div class="mb-8 space-y-4">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Unlock Bootloader</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="bootloader" name="bootloader" value="1" {{ $bootloader ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                </div>
                            </label>
                            
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Turnip Driver Support</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="turnip" name="turnip" value="1" {{ $turnip ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                </div>
                            </label>
                        </div>

                        <button id="apply-filters"
                            class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-teal-500/20 active:scale-95">
                            Apply Filters
                        </button>
                    </div>
                </div>
                @endif

                <!-- Main Content -->
                <div class="{{ $tab == 'overall' ? 'lg:col-span-3' : 'lg:col-span-4' }} space-y-6">
                    <div class="flex flex-col md:flex-row justify-end items-start md:items-center gap-4">
                        <!-- Tabs -->
                        <div id="tabs-container" class="bg-gray-200 dark:bg-white/10 p-1.5 rounded-xl inline-flex font-bold text-sm overflow-x-auto max-w-full">
                    <a href="{{ route('phones.rankings', ['tab' => 'overall']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'overall' ? 'bg-white dark:bg-black shadow-sm text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Expert Score
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'ueps']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'ueps' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        UEPS 45
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'performance']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'performance' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Performance
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'gaming']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'gaming' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Gaming (GPX)
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'cms']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'cms' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Camera (CMS)
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'endurance']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'endurance' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Endurance
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'value']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'value' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Value
                    </a>
                </div>
            </div>

            <!-- Data Table -->
            <div id="rankings-table-container"
                class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            @if ($tab == 'overall')
                                <!-- Expert Score Info Card -->
                                <th colspan="7" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-indigo-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-500/30">Definitive Ranking</span>
                                                    <h3 class="text-xl font-bold text-white">Expert Score</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The comprehensive <strong>Expert Score</strong> identifies the absolute best device by analyzing key specs and performance metrics.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            @elseif ($tab == 'ueps')
                                <!-- UEPS Info Card -->
                                <th colspan="7" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-teal-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold border border-teal-500/30">Methodology</span>
                                                    <h3 class="text-xl font-bold text-white">What is UEPS 45?</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>Ultra-Extensive Phone Scoring System (UEPS-45)</strong>
                                                    evaluates devices on a 255-point scale across 40+ touchpoints, including
                                                    real-world build quality, display efficiency, sustained performance, and
                                                    camera versatility.
                                                </p>
                                            </div>
                                            <a href="{{ route('methodology.ueps') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Methodology
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'performance')
                                <!-- FPI Info Card -->
                                <th colspan="8" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-blue-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-500/30">Methodology</span>
                                                    <h3 class="text-xl font-bold text-white">What is FPI?</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>Final Performance Index (FPI)</strong> is a weighted metric
                                                    combining <strong>AnTuTu v11 (40%)</strong>, <strong>Geekbench
                                                        (40%)</strong>, and <strong>3DMark (20%)</strong> scores to provide
                                                    a single, normalized performance rating.
                                                </p>
                                            </div>
                                            <a href="{{ route('methodology.fpi') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Formula
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'gaming')
                                <!-- GPX Info Card -->
                                <th colspan="9" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-red-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-red-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold border border-red-500/30">New
                                                        Standard</span>
                                                    <h3 class="text-xl font-bold text-white">GPX-300 Gaming Index</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The definitive 300-point competitive gaming evaluation system.
                                                    Prioritizing <strong>Sustained Performance</strong>,
                                                    <strong>Thermals</strong>, and <strong>Input Latency</strong> over peak
                                                    burst benchmarks.
                                                </p>
                                            </div>
                                            <a href="{{ route('methodology.gpx') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Methodology
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'cms')
                                <!-- CMS Info Card -->
                                <th colspan="8" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-amber-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">New
                                                        Standard</span>
                                                    <h3 class="text-xl font-bold text-white">Camera Mastery Score (CMS-1330)
                                                    </h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>CMS-1330</strong> evaluates the complete imaging system:
                                                    Sensor size, Optical Stability, Video capabilities, and real-world
                                                    benchmarks like <strong>DxOMark</strong> and
                                                    <strong>PhoneArena</strong>.
                                                </p>
                                            </div>
                                            <a href="{{ route('methodology.cms') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Methodology
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'endurance')
                                <!-- Endurance Info Card -->
                                <th colspan="8" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-purple-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold border border-purple-500/30">New</span>
                                                    <h3 class="text-xl font-bold text-white">Endurance Score</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    Calculated based on <strong>Battery Capacity (mAh)</strong> and
                                                    <strong>Active Use Hours</strong>, normalizing legacy endurance ratings
                                                    for modern contexts.
                                                </p>
                                            </div>
                                            <a href="{{ route('methodology.endurance') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Methodology
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'value')
                                <!-- Value Info Card -->
                                <th colspan="9" class="p-0 border-b-0">
                                    <div
                                        class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div
                                            class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-emerald-500/30 transition-colors duration-500">
                                        </div>
                                        <div
                                            class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">New
                                                        Standard</span>
                                                    <h3 class="text-xl font-bold text-white">Bang for Buck Index</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>Bang for Buck Index</strong> evaluates price-to-performance
                                                    using a weighted formula:
                                                    <strong>UEPS (25%)</strong>, <strong>FPI (25%)</strong>, <strong>CMS
                                                        (25%)</strong>,
                                                    <strong>GPX (15%)</strong>, and <strong>Endurance (10%)</strong>.
                                                </p>
                                            </div>
                                            <a href="{{ route('docs.index') }}"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Formula
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @endif

                            </tr>
                            <tr
                                class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-bold transition-colors duration-300">
                                <th
                                    class="px-6 py-4 sticky left-0 bg-gray-50 dark:bg-[#181818] z-10 w-16 text-center text-xs font-bold text-gray-500 uppercase tracking-wider transition-colors duration-300">
                                    #</th>
                                <th
                                    class="px-6 py-4 sticky left-16 bg-gray-50 dark:bg-[#181818] z-10 text-xs font-bold text-gray-500 uppercase tracking-wider transition-colors duration-300">
                                    Phone</th>

                                <!-- Common: Price -->
                                <th
                                    class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price', 'direction' => $sort == 'price' && $direction == 'asc' ? 'desc' : 'asc']) }}"
                                        class="flex items-center gap-1">
                                        Price
                                        @if ($sort == 'price')
                                            <span class="text-teal-500">{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="opacity-0 group-hover:opacity-30">↓</span>
                                        @endif
                                    </a>
                                </th>

                                @if ($tab == 'overall')
                                    <!-- Overall/Expert Columns -->
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        SoC
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Config
                                    </th>
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'expert_score', 'direction' => $sort == 'expert_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-indigo-600 dark:text-indigo-400">
                                            Expert Score
                                            @if ($sort == 'expert_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Buy
                                    </th>
                                @endif

                                @if ($tab == 'ueps')
                                    <!-- UEPS Columns -->
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'ueps_score', 'direction' => $sort == 'ueps_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-teal-600 dark:text-teal-400">
                                            UEPS 45
                                            @if ($sort == 'ueps_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_ueps', 'direction' => $sort == 'price_per_ueps' && $direction == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Price / Point
                                            @if ($sort == 'price_per_ueps')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                @elseif($tab == 'performance')
                                    <!-- Performance Columns -->
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'overall_score', 'direction' => $sort == 'overall_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-blue-600 dark:text-blue-400">
                                            FPI Score
                                            @if ($sort == 'overall_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_fpi', 'direction' => $sort == 'price_per_fpi' && $direction == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Price / Point
                                            @if ($sort == 'price_per_fpi')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'antutu_score', 'direction' => $sort == 'antutu_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            AnTuTu
                                            @if ($sort == 'antutu_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'geekbench_multi', 'direction' => $sort == 'geekbench_multi' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Geekbench
                                            @if ($sort == 'geekbench_multi')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="p-5 text-right">3DMark</th>
                                @elseif($tab == 'gaming')
                                    <!-- Gaming Columns -->
                                    <th
                                        class="px-2 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right whitespace-nowrap">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'gpx_score', 'direction' => $sort == 'gpx_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-red-600 dark:text-red-400">
                                            GPX
                                            @if ($sort == 'gpx_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="SoC & GPU Power (70)">SoC</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Sustained Performance (50)">Susp</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Gaming Display (40)">Disp</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Memory & Storage (25)">Mem</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Battery & Charging (25)">Batt</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Gaming Software (30)">Soft</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Connectivity (20)">Conn</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Audio & Haptics (10)">Aud</th>
                                    <th class="px-2 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        title="Emulator & Developer (30)">Emu</th>
                                @elseif($tab == 'cms')
                                    <!-- CMS Columns -->
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'cms_score', 'direction' => $sort == 'cms_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-amber-600 dark:text-amber-400">
                                            CMS Score
                                            @if ($sort == 'cms_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_cms', 'direction' => $sort == 'price_per_cms' && $direction == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Price / Point
                                            @if ($sort == 'price_per_cms')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'dxomark_score', 'direction' => $sort == 'dxomark_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            DxOMark
                                            @if ($sort == 'dxomark_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'phonearena_camera_score', 'direction' => $sort == 'phonearena_camera_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            PhoneArena
                                            @if ($sort == 'phonearena_camera_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="p-5 text-right text-xs font-bold text-gray-500 uppercase">Primary Sensor
                                    </th>
                                @elseif($tab == 'endurance')
                                    <!-- Endurance Columns -->
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'endurance_score', 'direction' => $sort == 'endurance_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-purple-600 dark:text-purple-400">
                                            Endurance Score
                                            @if ($sort == 'endurance_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_endurance', 'direction' => $sort == 'price_per_endurance' && $direction == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Price / Point
                                            @if ($sort == 'price_per_endurance')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'battery_endurance_hours', 'direction' => $sort == 'battery_endurance_hours' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1">
                                            Active Use
                                            @if ($sort == 'battery_endurance_hours')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="p-5 text-right text-xs font-bold text-gray-500 uppercase">Capacity</th>
                                    <th class="p-5 text-right text-xs font-bold text-gray-500 uppercase">Charging</th>
                                    @elseif($tab == 'value')
                                    <!-- Value Columns -->
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'value_score', 'direction' => $sort == 'value_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-emerald-600 dark:text-emerald-400">
                                            Value Score
                                            @if ($sort == 'value_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="p-5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors duration-300 group text-right">
                                        <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'endurance_score', 'direction' => $sort == 'endurance_score' && $direction == 'desc' ? 'asc' : 'desc']) }}"
                                            class="flex items-center justify-end gap-1 text-purple-600 dark:text-purple-400">
                                            Endurance
                                            @if ($sort == 'endurance_score')
                                                <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="p-5 text-right font-bold text-blue-600 dark:text-blue-400">FPI</th>
                                    <th class="p-5 text-right font-bold text-teal-600 dark:text-teal-400">UEPS</th>
                                    <th class="p-5 text-right font-bold text-amber-600 dark:text-amber-400">CMS</th>
                                    <th class="p-5 text-right font-bold text-red-600 dark:text-red-400">GPX</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-white/5 text-sm font-medium text-gray-700 dark:text-gray-300">
                            @foreach ($phones as $index => $phone)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#181818] transition-colors duration-300 group">
                                    <td
                                        class="px-6 py-5 sticky left-0 bg-white dark:bg-[#121212] group-hover:bg-gray-50 dark:group-hover:bg-[#181818] text-center font-bold text-gray-400 transition-colors duration-300">
                                        #{{ $ranks[$phone->id] ?? '-' }}
                                    </td>
                                    <td
                                        class="px-6 py-5 sticky left-16 bg-white dark:bg-[#121212] group-hover:bg-gray-50 dark:group-hover:bg-[#181818] transition-colors duration-300">
                                        <a href="{{ route('phones.show', $phone) }}" class="flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 bg-gray-100 dark:bg-white/5 rounded-xl flex items-center justify-center p-1.5 border border-gray-200 dark:border-white/5 transition-colors duration-300">
                                                @if ($phone->image_url)
                                                    <img src="{{ $phone->image_url }}" alt=""
                                                        class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal transition-opacity duration-300">
                                                @endif
                                            </div>
                                            <div>
                                                <div
                                                    class="font-bold text-gray-900 dark:text-white text-base leading-tight">
                                                    {{ $phone->name }}</div>
                                                <div class="text-xs text-gray-500 font-normal">{{ $phone->model_variant }}
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 font-mono text-gray-600 dark:text-gray-400">
                                        ₹{{ number_format($phone->price) }}</td>

                                    @if ($tab == 'overall')
                                        <td class="px-6 py-5 text-left text-sm text-gray-600 dark:text-gray-400">
                                            {{ $phone->platform->chipset ?? '-' }}
                                        </td>
                                        <td class="px-6 py-5 text-left text-sm text-gray-600 dark:text-gray-400">
                                            {{ $phone->platform->ram ?? '-' }} / {{ $phone->platform->internal_storage ?? '-' }}
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 font-bold text-base border border-indigo-200 dark:border-indigo-800 transition-colors duration-300">
                                                {{ $phone->expert_score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @if ($phone->amazon_url)
                                                    <a href="{{ $phone->amazon_url }}" target="_blank" rel="nofollow noopener"
                                                        class="hover:scale-110 transition-transform block" title="Buy on Amazon">
                                                        <img src="{{ asset('assets/amazon-icon.png') }}" alt="Amazon" 
                                                             style="width: 36px; height: 36px; background-color: white; padding: 4px; border-radius: 8px; object-fit: contain;">
                                                    </a>
                                                @endif
                                                @if ($phone->flipkart_url)
                                                    <a href="{{ $phone->flipkart_url }}" target="_blank" rel="nofollow noopener"
                                                        class="hover:scale-110 transition-transform block" title="Buy on Flipkart">
                                                        <img src="{{ asset('assets/flipkart-icon.png') }}" alt="Flipkart" 
                                                             style="width: 36px; height: 36px; background-color: white; padding: 4px; border-radius: 8px; object-fit: contain;">
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    @elseif ($tab == 'ueps')
                                        <td class="px-6 py-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 font-bold border border-teal-100 dark:border-teal-800 transition-colors duration-300">
                                                {{ $phone->ueps_score ?? '-' }}
                                                <span class="text-[10px] opacity-60 font-normal">/255</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right font-mono text-gray-500">
                                            ₹{{ $phone->ueps_score > 0 ? number_format($phone->price / $phone->ueps_score) : '-' }}
                                        </td>
                                    @elseif($tab == 'performance')
                                        <td class="p-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 font-bold border border-blue-100 dark:border-blue-800 transition-colors duration-300">
                                                {{ $phone->overall_score }}
                                                <span class="text-[10px] opacity-60 font-normal">/100</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right font-mono text-gray-500">
                                            ₹{{ $phone->overall_score > 0 ? number_format($phone->price / $phone->overall_score) : '-' }}
                                        </td>
                                        <td
                                            class="p-5 text-right font-mono {{ $phone->benchmarks && $phone->benchmarks->antutu_score > 2000000 ? 'text-green-600 dark:text-green-400 font-bold' : '' }}">
                                            {{ $phone->benchmarks ? number_format($phone->benchmarks->antutu_score) : '-' }}
                                        </td>
                                        <td class="p-5 text-right font-mono">
                                            {{ $phone->benchmarks ? number_format($phone->benchmarks->geekbench_multi) : '-' }}
                                        </td>
                                        <td class="p-5 text-right font-mono text-orange-600 dark:text-orange-400">
                                            {{ $phone->benchmarks ? number_format($phone->benchmarks->dmark_wild_life_extreme ?? 0) : '-' }}
                                        </td>
                                    @elseif($tab == 'gaming')
                                        <td class="px-2 py-3 text-right">
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 font-bold border border-red-100 dark:border-red-800 transition-colors duration-300">
                                                {{ $phone->gpx_score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['soc_gpu']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['sustained']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['display']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['memory']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['battery']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['software']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['connectivity']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['audio']['score'] ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-gray-500 font-mono">
                                            {{ $phone->gpx_details['emulator']['score'] ?? '-' }}
                                        </td>
                                    @elseif($tab == 'cms')
                                        <td class="p-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 font-bold border border-amber-100 dark:border-amber-800 transition-colors duration-300">
                                                {{ $phone->cms_score }}
                                                <span class="text-[10px] opacity-60 font-normal">/1330</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right font-mono text-gray-500">
                                            ₹{{ $phone->cms_score > 0 ? number_format($phone->price / $phone->cms_score) : '-' }}
                                        </td>
                                        <td class="p-5 text-right font-mono text-orange-600 dark:text-orange-400">
                                            {{ $phone->benchmarks && $phone->benchmarks->dxomark_score ? $phone->benchmarks->dxomark_score : '-' }}
                                        </td>
                                        <td class="p-5 text-right font-mono text-blue-600 dark:text-blue-400">
                                            {{ $phone->benchmarks && $phone->benchmarks->phonearena_camera_score ? $phone->benchmarks->phonearena_camera_score : '-' }}
                                        </td>
                                        <td class="p-5 text-right text-xs">
                                            {{ $phone->camera && $phone->camera->main_camera_sensors ? explode(',', $phone->camera->main_camera_sensors)[0] : '-' }}
                                        </td>
                                    @elseif($tab == 'endurance')
                                        <td class="p-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 font-bold border border-purple-100 dark:border-purple-800 transition-colors duration-300">
                                                {{ $phone->calculateEnduranceScore() }}
                                                @if ($phone->calculateEnduranceScore() >= 100)
                                                    <span class="text-[10px]">👑</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right font-mono text-gray-500">
                                            ₹{{ $phone->calculateEnduranceScore() > 0 ? number_format($phone->price / $phone->calculateEnduranceScore()) : '-' }}
                                        </td>
                                        <td
                                            class="p-5 text-right font-mono {{ ($phone->benchmarks && (($phone->benchmarks->battery_active_use_score && floatval($phone->benchmarks->battery_active_use_score) > 13) || $phone->benchmarks->battery_endurance_hours > 110)) ? 'text-green-600 dark:text-green-400 font-bold' : '' }}">
                                            @if($phone->benchmarks && $phone->benchmarks->battery_active_use_score)
                                                {{ $phone->benchmarks->battery_active_use_score }}
                                            @elseif($phone->benchmarks && $phone->benchmarks->battery_endurance_hours)
                                                {{ $phone->benchmarks->battery_endurance_hours . 'h' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-5 text-right font-mono text-gray-600 dark:text-gray-400">
                                            {{ $phone->battery->battery_type ?? '-' }}
                                        </td>
                                        <td class="p-5 text-right font-mono text-xs text-gray-500">
                                            {{ $phone->battery->charging_wired ?? '-' }}
                                        </td>
                                    @elseif($tab == 'value')
                                        <td class="px-6 py-5 text-right">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 font-bold text-base border border-emerald-200 dark:border-emerald-800 transition-colors duration-300">
                                                {{ $phone->value_score }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-5 text-right font-mono font-bold text-purple-600 dark:text-purple-400">
                                            {{ $phone->endurance_score ?? $phone->calculateEnduranceScore() }}
                                        </td>
                                        <td
                                            class="px-6 py-5 text-right font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $phone->overall_score }}
                                        </td>
                                        <td
                                            class="px-6 py-5 text-right font-mono font-bold text-teal-600 dark:text-teal-400">
                                            {{ $phone->ueps_score ?? '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-5 text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                                            {{ $phone->cms_score ?? '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-5 text-right font-mono font-bold text-red-600 dark:text-red-400">
                                            {{ $phone->gpx_score ?? '-' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="bg-gray-50 dark:bg-white/5 border-t border-gray-200 dark:border-white/5 p-4 transition-colors duration-300">
                    {{ $phones->links() }}
                </div>
            </div>
            
            </div> <!-- End Main Content -->
        </div> <!-- End Grid -->

        </div>
    </div>



    <style>
        /* Force ToolCool Slider Handle to White */
        tc-range-slider {
            --pointer-bg: #ffffff !important;
            --pointer-bg-hover: #ffffff !important;
            --pointer-bg-focus: #ffffff !important;
            --pointer-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
            --pointer-shadow-hover: 0 2px 5px rgba(0,0,0,0.4) !important;
            --pointer-shadow-focus: 0 2px 5px rgba(0,0,0,0.4) !important;
        }
    </style>

    <script>
        function initFilters() {
            // Select buttons
            const applyBtn = document.getElementById('apply-filters');
            const resetBtn = document.getElementById('reset-filters');
            
            // --- ToolCool Slider Event Listeners ---
            const attachSliderListeners = (sliderId, minInputId, maxInputId, minDisplayId, maxDisplayId, formatFn) => {
                const slider = document.getElementById(sliderId);
                const minInput = document.getElementById(minInputId);
                const maxInput = document.getElementById(maxInputId);
                const minDisplay = document.getElementById(minDisplayId);
                const maxDisplay = document.getElementById(maxDisplayId);

                if (slider) {
                    slider.addEventListener('change', (evt) => {
                        const val1 = evt.detail.value1;
                        const val2 = evt.detail.value2;

                        if (minInput) minInput.value = val1;
                        if (maxInput) maxInput.value = val2;

                        if (minDisplay) minDisplay.textContent = formatFn(val1);
                        if (maxDisplay) maxDisplay.textContent = formatFn(val2);
                    });
                }
            };

            const formatCurrency = (val) => '₹' + Math.round(val).toLocaleString('en-IN');
            const formatGB = (val) => Math.round(val) + ' GB';
            const formatStorage = (val) => {
                return val < 1000 ? Math.round(val) + ' GB' : (Math.round(val) / 1024).toFixed(0) + ' TB';
            };

            attachSliderListeners('price-slider', 'min_price', 'max_price', 'price-min-display', 'price-max-display', formatCurrency);
            attachSliderListeners('ram-slider', 'min_ram', 'max_ram', 'ram-min-display', 'ram-max-display', formatGB);
            attachSliderListeners('storage-slider', 'min_storage', 'max_storage', 'storage-min-display', 'storage-max-display', formatStorage);

            // --- Apply Filters Button ---
            if (applyBtn) {
                applyBtn.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('page');
                    
                    const curMinPrice = document.getElementById('min_price');
                    const curMaxPrice = document.getElementById('max_price');
                    const curMinRam = document.getElementById('min_ram');
                    const curMaxRam = document.getElementById('max_ram');
                    const curMinStorage = document.getElementById('min_storage');
                    const curMaxStorage = document.getElementById('max_storage');
                    const curBootloader = document.getElementById('bootloader');
                    const curTurnip = document.getElementById('turnip');

                    if (curMinPrice) url.searchParams.set('min_price', curMinPrice.value);
                    if (curMaxPrice) url.searchParams.set('max_price', curMaxPrice.value);
                    if (curMinRam) url.searchParams.set('min_ram', curMinRam.value);
                    if (curMaxRam) url.searchParams.set('max_ram', curMaxRam.value);
                    if (curMinStorage) url.searchParams.set('min_storage', curMinStorage.value);
                    if (curMaxStorage) url.searchParams.set('max_storage', curMaxStorage.value);
                    
                    if (curBootloader) {
                        if (curBootloader.checked) url.searchParams.set('bootloader', '1');
                        else url.searchParams.delete('bootloader');
                    }
                    
                    if (curTurnip) {
                        if (curTurnip.checked) url.searchParams.set('turnip', '1');
                        else url.searchParams.delete('turnip');
                    }
 
                    window.location.href = url.toString();
                });
            }

            if(resetBtn) {
                 resetBtn.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    const params = ['min_price', 'max_price', 'min_ram', 'max_ram', 'min_storage', 'max_storage', 'bootloader', 'turnip', 'page'];
                    params.forEach(p => url.searchParams.delete(p));
                    window.location.href = url.toString();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFilters);
        } else {
            initFilters();
        }
    </script>
@endsection
