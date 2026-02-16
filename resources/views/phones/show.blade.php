@extends('layouts.app')

@section('content')
    <div
        class="bg-gray-50 dark:bg-[#0a0a0a] min-h-screen pt-14 pb-12 font-sans text-gray-900 dark:text-gray-100 selection:bg-teal-500 selection:text-white animate-fadeInUp">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav
                class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-8 transition-colors hover:text-teal-600 dark:hover:text-teal-400">
                <a href="{{ route('home') }}" class="flex items-center gap-1 group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Phones
                </a>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                <!-- LEFT COLUMN: Visuals & Scores (Sticky) -->
                <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6 h-fit order-first lg:order-1">

                    <!-- Phone Image Card -->
                    <div
                        class="relative w-full aspect-[4/5] bg-white dark:bg-[#121212] rounded-[2.5rem] shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10 flex items-center justify-center p-10 overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-gray-50 to-transparent dark:from-white/5 dark:to-transparent opacity-50">
                        </div>
                        <!-- Subtle animated glow behind phone -->
                        <div
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-teal-500/20 rounded-full blur-[64px] group-hover:bg-teal-500/30 transition-colors duration-700">
                        </div>

                        @if ($phone->image_url)
                            <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}"
                                class="relative z-10 w-full h-full object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500 ease-out will-change-transform">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-300 dark:text-gray-700">
                                <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-sm font-medium">No Image</span>
                            </div>
                        @endif
                    </div>

                    <!-- Scores Grid moved from Right Column -->
                    <!-- Scores Grid moved from Right Column -->
                    <div class="relative bg-gray-900 dark:bg-black rounded-3xl p-6 text-white shadow-lg ring-1 ring-white/10 group"
                        style="background-color: #111827; color: white;">
                        <!-- Background Blur Container (Clipped) -->
                        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                            <div
                                class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-teal-600 rounded-full blur-[60px] opacity-30 group-hover:opacity-50 transition-opacity">
                            </div>
                        </div>

                        <div class="relative z-20 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold uppercase tracking-widest text-teal-300">UEPS Score</h3>
                                    <div class="relative group/tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 text-teal-400/70 hover:text-teal-300 cursor-help transition-colors">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900 text-white text-xs rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none shadow-xl border border-white/10 text-center">
                                            Ultra-Extensive Phone Scoring System (UEPS-45) is a 255-point system evaluating
                                            Display, Gaming, Battery, Camera, and Connectivity based on 40+ touchpoints.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span
                                        class="text-6xl font-black tracking-tighter text-white">{{ $phone->ueps_details['total_score'] }}</span>
                                    <span class="text-lg font-medium text-gray-400">/255</span>
                                </div>
                                <div
                                    class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold border border-white/10 backdrop-blur-md text-teal-200">
                                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                                    {{ $phone->ueps_details['grade'] ?? 'Enthusiast' }} Grade
                                </div>
                            </div>

                            <!-- Radial Chart Mini -->
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full -rotate-90">
                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="6"
                                        fill="transparent" class="text-white/10" />
                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="6"
                                        fill="transparent" stroke-dasharray="264"
                                        stroke-dashoffset="{{ 264 - (264 * $phone->ueps_details['percentage']) / 100 }}"
                                        class="text-teal-500 transition-all duration-1000 ease-out"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    class="absolute inset-0 flex items-center justify-center text-sm font-bold text-white">{{ $phone->ueps_details['percentage'] }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- GPX Score Grid -->
                    <div class="relative bg-zinc-900 dark:bg-black rounded-3xl p-6 text-white shadow-lg ring-1 ring-white/10 group mt-6"
                        style="background-color: #111827; color: white;">
                        <!-- Background Blur Container (Clipped) -->
                        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                            <div
                                class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-red-600 rounded-full blur-[60px] opacity-30 group-hover:opacity-50 transition-opacity">
                            </div>
                        </div>

                        <div class="relative z-20 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold uppercase tracking-widest text-red-300">GPX Score</h3>
                                    <div class="relative group/tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 text-red-400/70 hover:text-red-300 cursor-help transition-colors">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900 text-white text-xs rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none shadow-xl border border-white/10 text-center">
                                            GPX-300 Gaming Index evaluates Sustained Performance, Thermals, and Input
                                            Latency for competitive gaming.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span
                                        class="text-6xl font-black tracking-tighter text-white">{{ $phone->gpx_score }}</span>
                                    <span class="text-lg font-medium text-gray-400">/300</span>
                                </div>
                                <div
                                    class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold border border-white/10 backdrop-blur-md text-red-200">
                                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                                    Gaming Performance
                                </div>
                            </div>

                            <!-- Radial Chart Mini -->
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full -rotate-90">
                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="6"
                                        fill="transparent" class="text-white/10" />
                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="6"
                                        fill="transparent" stroke-dasharray="264"
                                        stroke-dashoffset="{{ 264 - (264 * $phone->gpx_score) / 300 }}"
                                        class="text-red-500 transition-all duration-1000 ease-out"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    class="absolute inset-0 flex items-center justify-center text-sm font-bold text-white">{{ round(($phone->gpx_score / 300) * 100) }}%</span>
                            </div>
                        </div>
                    </div>

                    @if ($phone->cms_score && $phone->cms_score > 0)
                        <!-- CMS Score Grid -->
                        <div class="relative bg-zinc-900 dark:bg-black rounded-3xl p-6 text-white shadow-lg ring-1 ring-white/10 group mt-6 transition-all hover:ring-amber-500/50"
                            style="background-color: #111827; color: white;">
                            <!-- Background Blur Container (Clipped) -->
                            <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                                <div
                                    class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-amber-600 rounded-full blur-[60px] opacity-20 group-hover:opacity-40 transition-opacity">
                                </div>
                            </div>

                            <div class="relative z-20 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-bold uppercase tracking-widest text-amber-300">CMS Camera
                                        </h3>
                                        <div class="relative group/tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor"
                                                class="w-4 h-4 text-amber-400/70 hover:text-amber-300 cursor-help transition-colors">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900 text-white text-xs rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none shadow-xl border border-white/10 text-center">
                                                Camera Mastery Score (CMS-1330) evaluates the complete imaging system
                                                including
                                                Sensor Size, Stability, Video, and Real-world Benchmarks.
                                                <div
                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span
                                            class="text-6xl font-black tracking-tighter text-white">{{ $phone->cms_score }}</span>
                                        <span class="text-lg font-medium text-gray-400">/1330</span>
                                    </div>
                                    <div
                                        class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold border border-white/10 backdrop-blur-md text-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                        Imaging System
                                    </div>
                                </div>

                                <!-- Radial Chart Mini -->
                                <div class="relative w-24 h-24">
                                    <svg class="w-full h-full -rotate-90">
                                        <circle cx="48" cy="48" r="42" stroke="currentColor"
                                            stroke-width="6" fill="transparent" class="text-white/10" />
                                        <circle cx="48" cy="48" r="42" stroke="currentColor"
                                            stroke-width="6" fill="transparent" stroke-dasharray="264"
                                            stroke-dashoffset="{{ 264 - (264 * $phone->cms_score) / 1330 }}"
                                            class="text-amber-500 transition-all duration-1000 ease-out"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span
                                        class="absolute inset-0 flex items-center justify-center text-sm font-bold text-white">{{ round(($phone->cms_score / 1330) * 100) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Detailed Breakdown Accordion -->
                    <div
                        class="bg-white dark:bg-[#121212] rounded-3xl shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden mt-6">
                        <div class="p-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                            <h3 class="font-bold text-gray-900 dark:text-white">Detailed Breakdown</h3>
                        </div>
                        <div>
                            @foreach ($phone->ueps_details['breakdown'] as $category => $data)
                                <div x-data="{ open: false }"
                                    class="group border-b border-gray-100 dark:border-white/5 last:border-0">
                                    <button @click="open = !open"
                                        class="w-full flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-xs font-bold">
                                                {{ $data['score'] }}</div>
                                            <span class="text-sm font-semibold">{{ $category }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                                            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div class="grid transition-all duration-300 ease-in-out"
                                        :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                        <div class="overflow-hidden">
                                            <div class="px-4 pb-4 pt-0">
                                                <div class="pl-[2.75rem] space-y-2">
                                                    @foreach ($data['details'] as $detail)
                                                        <div class="flex justify-between items-start text-xs">
                                                            <span
                                                                class="text-gray-500 w-2/3">{{ $detail['criterion'] }}</span>
                                                            <div class="text-right">
                                                                <span
                                                                    class="font-bold block {{ $detail['points'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">{{ $detail['points'] > 0 ? '+' . $detail['points'] : '0' }}</span>
                                                                <span
                                                                    class="text-[10px] text-gray-400">{{ $detail['reason'] }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- CMS Breakdown Accordion Item -->
                            <div x-data="{ open: false }"
                                class="group border-b border-gray-100 dark:border-white/5 last:border-0 border-t border-amber-100 dark:border-amber-900/30">
                                <button @click="open = !open"
                                    class="w-full flex items-center justify-between p-4 cursor-pointer hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors text-left">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 flex items-center justify-center text-xs font-bold">
                                            {{ $phone->cms_score }}</div>
                                        <span class="text-sm font-bold text-amber-900 dark:text-amber-100">Camera Mastery
                                            (CMS)</span>
                                    </div>
                                    <svg class="w-4 h-4 text-amber-400 transition-transform duration-300"
                                        :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="grid transition-all duration-300 ease-in-out"
                                    :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                    <div class="overflow-hidden">
                                        <div class="px-4 pb-4 pt-0">
                                            <div class="pl-[2.75rem] space-y-4">
                                                @if ($phone->cms_details && isset($phone->cms_details['breakdown']))
                                                    @foreach ($phone->cms_details['breakdown'] as $key => $data)
                                                        <div>
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span
                                                                    class="text-xs font-bold text-gray-500 uppercase">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                                                <span
                                                                    class="text-xs font-bold text-gray-900 dark:text-white">{{ $data['score'] }}<span
                                                                        class="text-gray-400">/{{ $data['max'] }}</span></span>
                                                            </div>
                                                            @foreach ($data['details'] as $detail)
                                                                @if ($detail['points'] > 0)
                                                                    <div
                                                                        class="flex justify-between items-start text-xs mb-1">
                                                                        <span
                                                                            class="text-gray-500 w-2/3 pl-2 border-l border-gray-200 dark:border-white/10">{{ $detail['criterion'] }}</span>
                                                                        <div class="text-right">
                                                                            <span
                                                                                class="font-bold block text-green-600 dark:text-green-400">+{{ $detail['points'] }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-xs text-gray-400 italic">No CMS breakdown data available
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Specs & Details -->
                <div class="lg:col-span-7 space-y-10 order-last lg:order-2">

                    <!-- Header -->
                    <div class="space-y-4">
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full bg-black/5 dark:bg-white/10 text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                            {{ $phone->brand }}
                        </div>
                        <!-- Fix: Prevent word wrap as requested -->
                        <h1
                            class="text-5xl md:text-7xl font-black tracking-tight text-gray-900 dark:text-white leading-tight">
                            {{ $phone->name }}
                        </h1>
                        <!-- Fix: Stacking on mobile to prevent overlap -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                            <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 font-medium tracking-tight">
                                {{ $phone->model_variant }}
                            </p>
                            <div class="flex items-center gap-4">
                                <p
                                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
                                    ₹{{ number_format($phone->price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Scores Grid (Top Request) -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- UEPS-45 Score Card Moved to Left Column -->
                        <!-- Value Score -->
                        <div class="bg-teal-600 dark:bg-teal-900 rounded-3xl p-5 text-white shadow-lg ring-1 ring-white/10 flex flex-col justify-between group h-32"
                            style="background-color: #0d9488; color: white;">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs font-bold uppercase tracking-widest opacity-80 text-white">Value
                                    </h3>
                                    <div class="relative group/tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 text-white/70 hover:text-white cursor-help transition-colors">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900 text-white text-xs rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none shadow-xl border border-white/10 text-center">
                                            Points per ₹10k. Higher is better. Calculated by dividing the UEPS score by the
                                            current price.
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 opacity-60 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span
                                    class="text-4xl font-black tracking-tighter block group-hover:scale-105 transition-transform origin-left text-white">{{ $phone->value_score }}</span>
                                <span class="text-[10px] uppercase font-bold opacity-60 text-white">Pts / ₹10k</span>
                            </div>
                        </div>

                        <!-- FPI Score -->
                        @php $fpi = $phone->calculateFPI(); @endphp
                        <div
                            class="bg-gray-100 dark:bg-white/5 rounded-3xl p-5 text-gray-900 dark:text-white shadow-sm ring-1 ring-black/5 dark:ring-white/10 flex flex-col justify-between h-32">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="text-xs font-bold uppercase tracking-widest opacity-60 text-gray-500 dark:text-gray-400">
                                        Perf. Index</h3>
                                    <div class="relative group/tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 text-gray-400/70 hover:text-gray-500 dark:hover:text-gray-300 cursor-help transition-colors">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div
                                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900 text-white text-xs rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 z-50 pointer-events-none shadow-xl border border-white/10 text-center">
                                            Raw performance capability based on synthetic benchmarks (AnTuTu, Geekbench,
                                            3DMark).
                                            <div
                                                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 opacity-40" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <span
                                    class="text-4xl font-black tracking-tighter block text-gray-900 dark:text-white">{{ is_array($fpi) ? $fpi['total'] : 0 }}</span>
                                <span class="text-[10px] uppercase font-bold opacity-40 text-gray-500 dark:text-gray-400">/
                                    100 Max</span>
                            </div>
                        </div>

                        <!-- Development Card (Moved to Top) -->
                        <div
                            class="col-span-2 bg-gray-900 dark:bg-black rounded-3xl p-6 text-white shadow-lg ring-1 ring-white/10 flex flex-col justify-center relative overflow-hidden group">
                            <!-- Background Glow -->
                            <div
                                class="absolute top-0 right-0 w-64 h-64 bg-green-500/10 rounded-full blur-[80px] group-hover:bg-green-500/20 transition-colors">
                            </div>

                            <div class="relative z-10 grid grid-cols-2 gap-x-8 gap-y-4">
                                <!-- Bootloader -->
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Bootloader</span>
                                    <span
                                        class="text-lg font-bold {{ $phone->platform->bootloader_unlockable ? 'text-green-400' : 'text-red-400' }}">{{ $phone->platform->bootloader_unlockable ? 'Unlockable' : 'Locked' }}</span>
                                </div>

                                <!-- Custom ROMs -->
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Custom
                                        ROMs</span>
                                    <span
                                        class="text-lg font-bold text-white">{{ $phone->platform->custom_rom_support }}</span>
                                </div>

                                <!-- Turnip -->
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Turnip</span>
                                    <span
                                        class="text-lg font-bold {{ $phone->platform->turnip_support ? 'text-purple-400' : 'text-gray-500' }}">{{ $phone->platform->turnip_support ? 'Yes' : 'No' }}</span>
                                </div>

                                <!-- AOSP Score -->
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">AOSP
                                        Aesthetics</span>
                                    <div class="flex items-baseline gap-1">
                                        <span
                                            class="text-lg font-bold text-yellow-400">{{ $phone->platform->aosp_aesthetics_score }}</span>
                                        <span class="text-xs text-gray-500 font-bold">/10</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buy Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if ($phone->amazon_url)
                            <a href="{{ $phone->amazon_url }}" target="_blank"
                                class="flex items-center justify-between p-4 bg-[#FF9900] hover:bg-[#ff8c00] text-white rounded-2xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 hover:-translate-y-1 transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white p-2 rounded-xl shrink-0">
                                        <img src="{{ asset('assets/amazon-icon.png') }}" class="w-6 h-6 object-contain"
                                            alt="Amazon">
                                    </div>
                                    <div class="leading-none text-white">
                                        <span class="block text-xs font-medium opacity-90 mb-0.5">Buy on</span>
                                        <span class="block text-lg font-bold">Amazon</span>
                                    </div>
                                </div>
                                @if ($phone->amazon_price)
                                    <span
                                        class="text-lg font-bold text-white">₹{{ number_format($phone->amazon_price) }}</span>
                                @endif
                            </a>
                        @endif

                        @if ($phone->flipkart_url)
                            <a href="{{ $phone->flipkart_url }}" target="_blank"
                                class="flex items-center justify-between p-4 bg-[#2874F0] hover:bg-[#1e6cd6] text-white rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 hover:-translate-y-1 transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white p-2 rounded-xl shrink-0">
                                        <img src="{{ asset('assets/flipkart-icon.png') }}" class="w-6 h-6 object-contain"
                                            alt="Flipkart">
                                    </div>
                                    <div class="leading-none text-white">
                                        <span class="block text-xs font-medium opacity-90 mb-0.5">Buy on</span>
                                        <span class="block text-lg font-bold">Flipkart</span>
                                    </div>
                                </div>
                                @if ($phone->flipkart_price)
                                    <span
                                        class="text-lg font-bold text-white">₹{{ number_format($phone->flipkart_price) }}</span>
                                @endif
                            </a>
                        @endif
                    </div>

                    <!-- Quick Specs Bento -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @if ($phone->platform)
                            <div
                                class="col-span-2 p-6 bg-white dark:bg-[#121212] rounded-[2rem] border border-gray-100 dark:border-white/5">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="p-2 rounded-lg bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Chipset</span>
                                </div>
                                <p class="text-xl font-bold leading-tight">{{ $phone->platform->chipset }}</p>
                            </div>
                        @endif

                        @if ($phone->body)
                            <div
                                class="p-6 bg-white dark:bg-[#121212] rounded-[2rem] border border-gray-100 dark:border-white/5">
                                <div class="mb-3 text-orange-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-bold">{{ $phone->body->display_size }}</p>
                                <p class="text-xs text-gray-500 mt-1 truncate font-medium">
                                    {{ $phone->body->display_type }}</p>
                            </div>
                        @endif

                        @if ($phone->battery)
                            <div
                                class="p-6 bg-white dark:bg-[#121212] rounded-[2rem] border border-gray-100 dark:border-white/5">
                                <div class="mb-3 text-emerald-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-bold">{{ $phone->battery->battery_type }}</p>
                                <p class="text-xs text-gray-500 mt-1 truncate font-medium">
                                    {{ $phone->battery->charging_wired }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detailed Specs Sections -->

                    <!-- 1. Launch, Design, Display & Body -->
                    <section>
                        <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                            <span>Design & Display</span>
                            <div class="h-px bg-gray-200 dark:bg-white/10 flex-1 ml-4"></div>
                        </h2>
                        <div
                            class="bg-white dark:bg-[#121212] rounded-[2rem] p-8 border border-gray-100 dark:border-white/5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- Launch Details (New) -->
                                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 mb-2">
                                    @if ($phone->announced_date)
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Announced</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $phone->announced_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                    @if ($phone->release_date)
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Released</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $phone->release_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                    @if ($phone->platform && $phone->platform->os_details)
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">OS
                                                Details</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $phone->platform->os_details }}</dd>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <h4
                                        class="text-sm font-bold uppercase text-teal-500 mb-6 tracking-wide border-b border-teal-500/20 pb-2">
                                        Display</h4>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Type
                                                & Size</dt>
                                            <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $phone->body->display_type }}</dd>
                                            <dd class="text-sm text-gray-500 mt-1">{{ $phone->body->display_size }} <span
                                                    class="mx-1">•</span> {{ $phone->body->screen_to_body_ratio ?? '' }}
                                            </dd>
                                            @if ($phone->body->screen_area)
                                                <dd class="text-xs text-gray-400 mt-0.5 font-medium">
                                                    {{ $phone->body->screen_area }}</dd>
                                            @endif
                                            @if ($phone->body->aspect_ratio)
                                                <dd class="text-xs text-gray-400 mt-0.5 font-medium">
                                                    {{ $phone->body->aspect_ratio }}</dd>
                                            @endif
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Resolution</dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300">
                                                {{ $phone->body->display_resolution }}</dd>
                                            @if ($phone->body->pixel_density)
                                                <dd class="text-xs text-gray-500 mt-1 font-medium">
                                                    {{ $phone->body->pixel_density }}</dd>
                                            @endif
                                        </div>
                                        @if ($phone->body->display_brightness || $phone->body->measured_display_brightness)
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    Brightness</dt>
                                                <dd class="text-base text-gray-700 dark:text-gray-300">
                                                    {{ $phone->body->display_brightness }}</dd>
                                                @if ($phone->body->measured_display_brightness)
                                                    <dd
                                                        class="text-xs text-amber-600 dark:text-amber-500 font-bold mt-1 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded inline-block">
                                                        Tested:
                                                        {{ \Illuminate\Support\Str::of($phone->body->measured_display_brightness)->replace(['max brightness', '(measured)'], '')->trim() }}
                                                    </dd>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($phone->body->pwm_dimming)
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    PWM Dimming</dt>
                                                <dd class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                    {{ $phone->body->pwm_dimming }}</dd>
                                            </div>
                                        @endif
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Protection</dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300">
                                                {{ $phone->body->display_protection ?? 'Not specified' }}</dd>
                                            @if ($phone->body->glass_protection_level)
                                                <dd class="text-xs text-teal-500 font-bold mt-1">
                                                    {{ $phone->body->glass_protection_level }}</dd>
                                            @endif
                                            @if ($phone->body->screen_glass)
                                                <dd class="text-xs text-gray-500 mt-0.5">{{ $phone->body->screen_glass }}
                                                </dd>
                                            @endif
                                        </div>
                                        @if ($phone->body->display_features)
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    Features</dt>
                                                <dd class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    {{ $phone->body->display_features }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold uppercase text-teal-500 mb-6 tracking-wide border-b border-teal-500/20 pb-2">
                                        Body</h4>
                                    <dl class="space-y-6">
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Build
                                            </dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                                                {{ $phone->body->build_material }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">SIM
                                            </dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                                                {{ $phone->body->sim }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Dimensions & Weight</dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                                {{ $phone->body->dimensions }}</dd>
                                            <dd class="text-base text-gray-700 dark:text-gray-300 mt-1 whitespace-nowrap">
                                                {{ $phone->body->weight }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Colors</dt>
                                            <dd class="text-base text-gray-700 dark:text-gray-300">
                                                {{ $phone->body->colors }}</dd>
                                        </div>
                                        @if ($phone->body->ip_rating)
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    IP Rating</dt>
                                                <dd
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold border border-blue-100 dark:border-blue-800">
                                                    {{ $phone->body->ip_rating }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Platform & Memory (NEW) -->
                    @if ($phone->platform)
                        <section>
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span>Platform & Hardware</span>
                                <div class="h-px bg-gray-200 dark:bg-white/10 flex-1 ml-4"></div>
                            </h2>
                            <div
                                class="bg-white dark:bg-[#121212] rounded-[2rem] p-8 border border-gray-100 dark:border-white/5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div>
                                        <h4
                                            class="text-sm font-bold uppercase text-teal-500 mb-6 tracking-wide border-b border-teal-500/20 pb-2">
                                            Platform</h4>
                                        <dl class="space-y-6">
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    OS</dt>
                                                <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $phone->platform->os }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Chipset</dt>
                                                <dd
                                                    class="text-lg font-bold text-gray-900 dark:text-gray-100 leading-snug">
                                                    {{ $phone->platform->chipset }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    CPU</dt>
                                                <dd
                                                    class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                                    {!! str_replace(' (', '<br>(', e($phone->platform->cpu)) !!}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    GPU</dt>
                                                <dd class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                    {{ $phone->platform->gpu }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold uppercase text-teal-500 mb-6 tracking-wide border-b border-teal-500/20 pb-2">
                                            Memory</h4>
                                        <dl class="space-y-6">
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Internal Storage</dt>
                                                <dd class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $phone->platform->internal_storage }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    RAM</dt>
                                                <dd class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $phone->platform->ram }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Storage Type</dt>
                                                <dd class="text-base text-gray-700 dark:text-gray-300 font-medium">
                                                    {{ $phone->platform->storage_type }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                                    Card Slot</dt>
                                                <dd class="text-base text-gray-700 dark:text-gray-300 font-medium">
                                                    {{ $phone->platform->memory_card_slot }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    <!-- 2. Performance & Benchmarks -->
                    @if ($phone->benchmarks)
                        <section>
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span>Performance</span>
                                <div class="h-px bg-gray-200 dark:bg-white/10 flex-1 ml-4"></div>
                            </h2>
                            <div
                                class="bg-white dark:bg-[#121212] rounded-[2rem] p-8 border border-gray-100 dark:border-white/5">

                                <!-- Antutu -->
                                <div class="mb-10">
                                    <div class="flex justify-between items-end mb-3">
                                        <span class="font-bold text-gray-500">AnTuTu Score</span>
                                        <span
                                            class="text-3xl font-black tracking-tight text-teal-600 dark:text-teal-400">{{ number_format($phone->benchmarks->antutu_score) }}</span>
                                    </div>
                                    <div class="h-5 w-full bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-600 w-0 animate-[fillBar_1s_ease-out_forwards]"
                                            style="width: {{ min(($phone->benchmarks->antutu_score / 3000000) * 100, 100) }}%">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                                    <!-- Geekbench -->
                                    <div>
                                        <div class="flex justify-between mb-3">
                                            <span class="font-bold text-gray-500 text-sm">Geekbench Multi</span>
                                            <span
                                                class="font-bold text-xl text-blue-600 dark:text-blue-400">{{ number_format($phone->benchmarks->geekbench_multi) }}</span>
                                        </div>
                                        <div
                                            class="h-3 w-full bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden mb-2">
                                            <div class="h-full bg-blue-500 rounded-full"
                                                style="width: {{ min(($phone->benchmarks->geekbench_multi / 10000) * 100, 100) }}%">
                                            </div>
                                        </div>
                                        <div class="text-right text-xs text-gray-400 font-medium">Single:
                                            {{ number_format($phone->benchmarks->geekbench_single) }}</div>
                                    </div>

                                    <!-- 3DMark -->
                                    <div>
                                        <div class="flex justify-between mb-3">
                                            <span class="font-bold text-gray-500 text-sm">3DMark Extreme</span>
                                            <span
                                                class="font-bold text-xl text-orange-600 dark:text-orange-400">{{ number_format($phone->benchmarks->dmark_wild_life_extreme ?? 0) }}</span>
                                        </div>
                                        <div class="h-3 w-full bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                                            <div class="h-full bg-orange-500 rounded-full"
                                                style="width: {{ min(($phone->benchmarks->dmark_wild_life_extreme / 8000) * 100, 100) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </section>
                    @endif

                    <!-- 3. Camera -->
                    @if ($phone->camera)
                        <section>
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span>Camera</span>
                                <div class="h-px bg-gray-200 dark:bg-white/10 flex-1 ml-4"></div>
                            </h2>
                            <div
                                class="bg-white dark:bg-[#121212] rounded-[2rem] p-8 border border-gray-100 dark:border-white/5">
                                <div class="space-y-10">
                                    <div class="pl-6 border-l-4 border-teal-500">
                                        <h4 class="text-sm font-bold uppercase text-teal-500 mb-2 tracking-wide">Main
                                            System</h4>
                                        <p class="text-xl font-medium leading-relaxed">
                                            {{ $phone->camera->main_camera_specs }}</p>

                                        @if ($phone->camera->main_camera_zoom)
                                            <div
                                                class="mt-2 inline-block px-3 py-1 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-lg text-sm font-bold">
                                                {{ $phone->camera->main_camera_zoom }}
                                            </div>
                                        @endif

                                        @if ($phone->camera->telephoto_camera_specs)
                                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                                                <h5 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-1">
                                                    Telephoto</h5>
                                                <p class="text-lg font-medium leading-relaxed">
                                                    {{ $phone->camera->telephoto_camera_specs }}</p>
                                            </div>
                                        @endif

                                        @if ($phone->camera->ultrawide_camera_specs)
                                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                                                <h5 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-1">
                                                    Ultrawide</h5>
                                                <p class="text-lg font-medium leading-relaxed">
                                                    {{ $phone->camera->ultrawide_camera_specs }}</p>
                                            </div>
                                        @endif

                                        <!-- New Granular Camera Specs -->
                                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                                            @if ($phone->camera->main_camera_sensors)
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Sensors</span>
                                                    <span
                                                        class="text-gray-700 dark:text-gray-300 font-medium leading-snug block">{{ $phone->camera->main_camera_sensors }}</span>
                                                </div>
                                            @endif
                                            @if ($phone->camera->main_camera_apertures)
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Aperture</span>
                                                    <span
                                                        class="text-gray-700 dark:text-gray-300 font-medium leading-snug block">{{ $phone->camera->main_camera_apertures }}</span>
                                                </div>
                                            @endif
                                            @if ($phone->camera->main_camera_focal_lengths)
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Focal
                                                        Length</span>
                                                    <span
                                                        class="text-gray-700 dark:text-gray-300 font-medium leading-snug block">{{ $phone->camera->main_camera_focal_lengths }}</span>
                                                </div>
                                            @endif
                                            @if ($phone->camera->main_camera_ois)
                                                <div>
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">OIS</span>
                                                    <span
                                                        class="text-gray-700 dark:text-gray-300 font-medium leading-snug block">{{ $phone->camera->main_camera_ois }}</span>
                                                </div>
                                            @endif
                                            @if ($phone->camera->main_camera_pdaf)
                                                <div class="col-span-1 sm:col-span-2">
                                                    <span
                                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Focus</span>
                                                    <span
                                                        class="text-gray-700 dark:text-gray-300 font-medium leading-snug block">{{ $phone->camera->main_camera_pdaf }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <p
                                            class="text-sm text-gray-500 mt-6 font-medium border-t border-gray-100 dark:border-white/5 pt-4">
                                            {{ $phone->camera->main_camera_features }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                        <div class="pl-6 border-l-4 border-teal-500">
                                            <h4 class="text-sm font-bold uppercase text-teal-500 mb-2 tracking-wide">Selfie
                                                Camera</h4>
                                            <p class="font-bold text-xl mb-3">{{ $phone->camera->selfie_camera_specs }}
                                            </p>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                                @if ($phone->camera->selfie_camera_aperture)
                                                    <div
                                                        class="flex justify-between border-b border-gray-100 dark:border-white/5 pb-1">
                                                        <span
                                                            class="font-bold text-xs uppercase text-gray-400">Aperture</span>
                                                        <span
                                                            class="font-medium text-gray-900 dark:text-gray-100">{{ $phone->camera->selfie_camera_aperture }}</span>
                                                    </div>
                                                @endif
                                                @if ($phone->camera->selfie_camera_sensor)
                                                    <div
                                                        class="flex justify-between border-b border-gray-100 dark:border-white/5 pb-1">
                                                        <span
                                                            class="font-bold text-xs uppercase text-gray-400">Sensor</span>
                                                        <span
                                                            class="font-medium text-gray-900 dark:text-gray-100">{{ $phone->camera->selfie_camera_sensor }}</span>
                                                    </div>
                                                @endif
                                                @if ($phone->camera->selfie_camera_autofocus)
                                                    <div class="pt-1"><span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 uppercase tracking-widest">Autofocus</span>
                                                    </div>
                                                @endif
                                                @if ($phone->camera->selfie_video_features)
                                                    <div class="pt-2 mt-2">
                                                        <span
                                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Selfie
                                                            Video</span>
                                                        <span
                                                            class="text-gray-900 dark:text-gray-100 font-medium block">{{ $phone->camera->selfie_video_features }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pl-6 border-l-4 border-gray-300 dark:border-gray-700">
                                            <h4 class="text-sm font-bold uppercase text-gray-500 mb-2 tracking-wide">Rear
                                                Camera Video</h4>
                                            <p
                                                class="text-base font-bold text-gray-900 dark:text-white leading-relaxed mb-2">
                                                {{ $phone->camera->main_video_capabilities }}</p>
                                            @if ($phone->camera->video_features)
                                                <p class="text-xs text-gray-500 font-medium">
                                                    {{ $phone->camera->video_features }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    <!-- 4. Enthusiast & Connectivity -->
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Development (Moved Up) / Connectivity & Audio -->
                        <div
                            class="bg-white dark:bg-[#121212] p-8 rounded-[2rem] border border-gray-100 dark:border-white/5 space-y-8">
                            @if ($phone->connectivity)
                                <div>
                                    <h3
                                        class="font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2 text-lg border-b border-gray-100 dark:border-white/5 pb-4">
                                        <svg class="w-5 h-5 text-teal-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                        </svg>
                                        Connectivity & Audio
                                    </h3>
                                    <ul class="space-y-4 text-sm">
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">WLAN</span>
                                            <div>
                                                <span
                                                    class="text-gray-900 dark:text-white font-bold block mb-1 leading-snug break-words whitespace-normal">{{ $phone->connectivity->wlan }}</span>
                                                @if ($phone->connectivity->wifi_bands)
                                                    <span
                                                        class="text-xs text-gray-500 font-medium">{{ $phone->connectivity->wifi_bands }}</span>
                                                @endif
                                            </div>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">BT</span>
                                            <span
                                                class="text-gray-900 dark:text-white font-bold">{{ $phone->connectivity->bluetooth }}</span>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">GPS</span>
                                            <div>
                                                @if ($phone->connectivity->positioning_details)
                                                    <span
                                                        class="text-gray-900 dark:text-white font-bold block mb-1 leading-snug">{{ $phone->connectivity->positioning_details }}</span>
                                                @else
                                                    <span
                                                        class="text-gray-900 dark:text-white font-bold block mb-1 leading-snug">{{ $phone->connectivity->positioning }}</span>
                                                @endif
                                            </div>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">NFC/IR</span>
                                            <span
                                                class="text-gray-900 dark:text-white font-bold">{{ $phone->connectivity->nfc }}
                                                / {{ $phone->connectivity->infrared }}</span>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">USB</span>
                                            <span
                                                class="text-gray-900 dark:text-white font-bold">{{ $phone->connectivity->usb_details ?? ($phone->connectivity->usb ?? 'Not specified') }}</span>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">Sensors</span>
                                            <span
                                                class="text-gray-700 dark:text-gray-300 font-medium leading-relaxed">{{ $phone->connectivity->sensors }}</span>
                                        </li>
                                        @if ($phone->connectivity->sar_value)
                                            <li class="grid grid-cols-[80px_1fr] gap-3">
                                                <span
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">SAR</span>
                                                <span
                                                    class="text-gray-700 dark:text-gray-300 font-medium">{{ $phone->connectivity->sar_value }}</span>
                                            </li>
                                        @endif

                                        <!-- Audio Specs -->
                                        <li
                                            class="grid grid-cols-[80px_1fr] gap-3 pt-4 mt-2 border-t border-gray-100 dark:border-white/5">
                                            <span
                                                class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-0.5">Audio</span>
                                            <div>
                                                <span
                                                    class="text-gray-900 dark:text-white font-bold block mb-2">{{ $phone->connectivity->audio_quality ?? 'High-Res Audio' }}</span>

                                                <div class="space-y-1 text-sm">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500 dark:text-gray-400">3.5mm Jack:</span>
                                                        <span
                                                            class="font-bold {{ $phone->connectivity->has_3_5mm_jack ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $phone->connectivity->has_3_5mm_jack ? 'Yes' : 'No' }}
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500 dark:text-gray-400">Speakers:</span>
                                                        <span class="font-bold text-gray-900 dark:text-white">
                                                            {{ $phone->connectivity->loudspeaker ? 'Stereo' : 'Mono' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Battery & Charging -->
                        <div
                            class="bg-white dark:bg-[#121212] p-8 rounded-[2rem] border border-gray-100 dark:border-white/5 space-y-8">
                            @if ($phone->battery)
                                <div>
                                    <h3
                                        class="font-bold mb-4 text-gray-900 dark:text-white flex items-center gap-2 text-lg">
                                        <svg class="w-5 h-5 text-teal-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Battery & Power
                                    </h3>
                                    <ul class="space-y-3 text-sm">
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider">Type</span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-white">{{ $phone->battery->battery_type }}</span>
                                        </li>
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider">Wired</span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-white">{{ $phone->battery->charging_wired }}</span>
                                        </li>
                                        @if (
                                            $phone->battery->charging_specs_detailed &&
                                                $phone->battery->charging_specs_detailed !== $phone->battery->charging_wired)
                                            <li class="grid grid-cols-[80px_1fr] gap-3">
                                                <span
                                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider">Speeds</span>
                                                <span
                                                    class="text-gray-700 dark:text-gray-300">{{ $phone->battery->charging_specs_detailed }}</span>
                                            </li>
                                        @endif
                                        <li class="grid grid-cols-[80px_1fr] gap-3">
                                            <span
                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider">Wireless</span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-white">{{ $phone->battery->charging_wireless ?? 'No' }}</span>
                                        </li>
                                        @if ($phone->battery->reverse_wireless || $phone->battery->reverse_wired)
                                            <li class="grid grid-cols-[80px_1fr] gap-3">
                                                <span
                                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider">Reverse</span>
                                                <span class="text-gray-700 dark:text-gray-300">
                                                    {{ $phone->battery->reverse_wireless ? 'Wireless: ' . $phone->battery->reverse_wireless : '' }}
                                                    {{ $phone->battery->reverse_wired ? ($phone->battery->reverse_wireless ? ' • ' : '') . 'Wired: ' . $phone->battery->reverse_wired : '' }}
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
@endsection
