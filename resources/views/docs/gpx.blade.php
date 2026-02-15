@extends('layouts.app')

@push('title', 'GPX-300 Gaming Index - PhoneFinderHub')
@push('description', 'The definitive 300-point competitive gaming evaluation system for modern smartphones.')

@section('content')
<div class="bg-gray-50 dark:bg-black min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 animate-fadeInUp">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                New Standard
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">
                Gaming Performance <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">eXtreme Index</span>
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                A 300-Point Competitive Gaming Evaluation System built for Native Android AAA, Competitive Esports, and High-End Emulation.
            </p>
        </div>

        <!-- Core Philosophy -->
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-8 mb-12 shadow-sm border border-gray-100 dark:border-white/5 animate-fadeInUp delay-100">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Why GPX-300?</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 mb-4">
                        Modern benchmarks like AnTuTu don't tell the full story. <strong class="text-slate-900 dark:text-white">GPX-300</strong> prioritizes stability, thermal endurance, and feature sets critical for serious gamers in 2026.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Sustained Performance > Peak Burst
                        </li>
                        <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Emulator Driver Support
                        </li>
                        <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Touch Latency & Input Lag
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-6 flex flex-col justify-center">
                    <div class="text-center">
                        <div class="text-5xl font-black text-slate-900 dark:text-white mb-2">300</div>
                        <div class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Points Available</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown -->
        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-8 text-center animate-fadeInUp delay-200">Category Breakdown</h2>

        <div class="space-y-6 animate-fadeInUp delay-300">
            <!-- 1. SoC & GPU -->
            <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 dark:text-red-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">SoC & GPU Power</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Raw processing capability</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">70 <span class="text-sm font-normal text-slate-400">pts</span></span>
                </div>
                <div class="grid md:grid-cols-2 gap-6 pl-16">
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white text-sm mb-2">GPU Tier (45 pts)</h4>
                        <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                            <li class="flex justify-between"><span>SD 8 Elite / Dimensity 9500</span> <span class="font-mono text-slate-900 dark:text-white">45 pts</span></li>
                            <li class="flex justify-between"><span>Snapdragon 8 Gen 3</span> <span class="font-mono text-slate-900 dark:text-white">30 pts</span></li>
                            <li class="flex justify-between"><span>Snapdragon 8 Gen 2</span> <span class="font-mono text-slate-900 dark:text-white">20 pts</span></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white text-sm mb-2">CPU Power (25 pts)</h4>
                         <p class="text-sm text-slate-600 dark:text-slate-400">Normalized formula: (Geekbench Multi × 0.6) + (Geekbench Single × 0.4)</p>
                    </div>
                </div>
            </div>

            <!-- 2. Sustained Performance -->
            <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Sustained Performance</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Long-term stability & cooling</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">50 <span class="text-sm font-normal text-slate-400">pts</span></span>
                </div>
                 <div class="grid md:grid-cols-2 gap-6 pl-16">
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white text-sm mb-2">Thermal Stability (30 pts)</h4>
                        <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                            <li class="flex justify-between"><span>95-100% Stability</span> <span class="font-mono text-slate-900 dark:text-white">30 pts</span></li>
                            <li class="flex justify-between"><span>85-94% Stability</span> <span class="font-mono text-slate-900 dark:text-white">22 pts</span></li>
                            <li class="flex justify-between"><span>< 75% Stability</span> <span class="font-mono text-slate-900 dark:text-white">5 pts</span></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 dark:text-white text-sm mb-2">Cooling Hardware (20 pts)</h4>
                        <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                            <li class="flex justify-between"><span>Active Fan Cooling</span> <span class="font-mono text-slate-900 dark:text-white">20 pts</span></li>
                             <li class="flex justify-between"><span>Dual Vapor Chamber</span> <span class="font-mono text-slate-900 dark:text-white">15 pts</span></li>
                        </ul>
                    </div>
                </div>
            </div>

             <!-- 3. Gaming Display -->
            <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Gaming Display</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Visual fluidity & response</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">40 <span class="text-sm font-normal text-slate-400">pts</span></span>
                </div>
                <div class="grid md:grid-cols-2 gap-6 pl-16">
                     <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-2">
                            <li class="flex justify-between"><span>165Hz+ Refresh Rate</span> <span class="font-mono text-slate-900 dark:text-white">10 pts</span></li>
                            <li class="flex justify-between"><span>1000Hz+ Touch Sampling</span> <span class="font-mono text-slate-900 dark:text-white">10 pts</span></li>
                            <li class="flex justify-between"><span>>3000 nits Brightness</span> <span class="font-mono text-slate-900 dark:text-white">10 pts</span></li>
                            <li class="flex justify-between"><span>2160Hz+ PWM Dimming</span> <span class="font-mono text-slate-900 dark:text-white">10 pts</span></li>
                     </ul>
                </div>
            </div>

            <!-- Other Categories Grid -->
            <div class="grid md:grid-cols-2 gap-6">
                 <!-- Memory -->
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Memory & Storage</h3>
                        <span class="font-bold">25 pts</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-2">UFS 4.0/4.1, LPDDR5X, 16GB+ RAM</p>
                </div>

                 <!-- Battery -->
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Battery & Charging</h3>
                        <span class="font-bold">25 pts</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-2">6000mAh+, 120W+ Charging, Bypass Charging support.</p>
                </div>

                <!-- Software -->
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Gaming Software</h3>
                        <span class="font-bold">30 pts</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-2">Dedicated gaming modes, FPS stabilizers, GPU driver updates.</p>
                </div>

                 <!-- Emulator -->
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-100 dark:border-white/5">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-white">Emulator Advantage</h3>
                        <span class="font-bold">30 pts</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-2">Snapdragon Elite/Turnip driver support, Root/ROM community.</p>
                </div>
            </div>

           
        </div>
    </div>
</div>
@endsection
