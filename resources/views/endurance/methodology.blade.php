@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-purple-500 selection:text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-sm font-bold mb-4 border border-purple-200 dark:border-purple-800">
                    BETA v1.0
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-6">
                    Endurance Score
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                    An adaptive scoring system that normalizes raw battery capacity and active use efficiency into a single,
                    comparable metric.
                </p>
            </div>

            <!-- Score Breakdown Visual -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-20 max-w-2xl mx-auto">
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-purple-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        50%</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Raw Capacity (mAh)</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-purple-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        50%</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Active Use Efficiency</div>
                </div>
            </div>

            <!-- Detailed Methodologies -->
            <div class="space-y-12">

                <!-- Section 1: Capacity Score -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Capacity Score</h2>
                        <p class="text-sm text-slate-500 mt-2">Points based purely on the battery size.</p>
                    </div>
                    <div class="p-8">
                        <div class="bg-purple-50 dark:bg-purple-900/10 p-6 rounded-2xl font-mono text-center">
                            <span class="text-slate-600 dark:text-slate-400">Score = </span>
                            <span class="text-purple-600 dark:text-purple-300 font-bold">Capacity (mAh) / 100</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-center text-sm">
                            <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                <div class="font-bold text-slate-900 dark:text-white">6000 mAh</div>
                                <div class="text-purple-600 font-bold">60 pts</div>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                <div class="font-bold text-slate-900 dark:text-white">5000 mAh</div>
                                <div class="text-purple-600 font-bold">50 pts</div>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                <div class="font-bold text-slate-900 dark:text-white">4500 mAh</div>
                                <div class="text-purple-600 font-bold">45 pts</div>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                                <div class="font-bold text-slate-900 dark:text-white">4000 mAh</div>
                                <div class="text-purple-600 font-bold">40 pts</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Adaptive Efficiency Score -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. Adaptive Efficiency Score</h2>
                        <p class="text-sm text-slate-500 mt-2">Normalizes different testing standards (Legacy vs. Active
                            Use)
                        </p>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Standard A: Active Use (Modern)</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Used for devices tested with modern
                                "Active Use" scripts (typically 10-20 hours).</p>
                            <div class="bg-green-50 dark:bg-green-900/10 p-4 rounded-xl font-mono text-center mb-4">
                                <span class="text-slate-600 dark:text-slate-400">Score = </span>
                                <span class="text-green-600 dark:text-green-400 font-bold">Hours × 3.5</span>
                            </div>
                            <div class="text-xs text-center text-slate-500">Example: 16h Active Use × 3.5 = <span
                                    class="font-bold">56 pts</span></div>
                        </div>

                        <div class="border-t border-slate-100 dark:border-white/5 pt-8">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Standard B: Legacy Endurance Rating
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Used for devices with older
                                "Endurance
                                Rating" tests (typically 80-150 hours).</p>
                            <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-xl font-mono text-center mb-4">
                                <span class="text-slate-600 dark:text-slate-400">Score = </span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold">Hours × 0.45</span>
                            </div>
                            <div class="text-xs text-center text-slate-500">Example: 120h Endurance Rating × 0.45 = <span
                                    class="font-bold">54 pts</span></div>
                        </div>
                    </div>
                </section>

                <!-- Final Formula -->
                <section
                    class="bg-gradient-to-br from-purple-900 to-indigo-900 dark:from-purple-900/40 dark:to-indigo-900/40 rounded-3xl shadow-xl overflow-hidden border border-purple-500/20">
                    <div class="p-8 text-center">
                        <h2 class="text-2xl font-bold text-white mb-6">🧮 Total Endurance Score</h2>
                        <div class="text-3xl font-mono font-bold text-white mb-2">
                            Capacity Score + Efficiency Score
                        </div>
                        <p class="text-purple-200">
                            (e.g., 50 + 56 = 106 pts)
                        </p>
                    </div>
                </section>

                <!-- Rating Tiers -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Rating Tiers</h2>
                    </div>
                    <div class="p-8 space-y-3">
                        <div
                            class="flex justify-between items-center p-4 rounded-xl bg-gradient-to-r from-purple-500/20 to-indigo-500/20 border border-purple-500/30">
                            <span class="font-bold text-slate-900 dark:text-white">100+</span>
                            <span class="text-sm font-bold text-purple-700 dark:text-purple-400">Marathon Runner 👑</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">90–99</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Excellent</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">80–89</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Great</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">70–79</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Good</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">
                                < 70 </span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Average / Weak</span>
                        </div>
                    </div>
                </section>

                <div class="text-center pt-8">
                    <a href="{{ route('phones.rankings', ['tab' => 'endurance']) }}"
                        class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold rounded-xl hover:scale-105 transition-transform shadow-lg shadow-purple-500/30">
                        Check Endurance Rankings →
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
