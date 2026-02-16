@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-purple-500 selection:text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-sm font-bold mb-4 border border-purple-200 dark:border-purple-800">
                    CMS-1330
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-6">
                    Camera Mastery Score
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                    A comprehensive 1330-point system evaluating smartphone camera hardware, imaging capabilities, and
                    real-world performance benchmarks.
                </p>
            </div>

            <!-- Score Breakdown Visual -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        240</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Sensor & Optics</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        90</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Resolution & Binning</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        80</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Focus & Stability</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        120</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Video System</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        50</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Multi-Camera Fusion</div>
                </div>
                <div
                    class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-purple-500/30 transition-colors group">
                    <div
                        class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-purple-500 transition-colors">
                        30</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Special Features</div>
                </div>
                <div
                    class="bg-gradient-to-br from-purple-500 to-pink-500 p-8 rounded-3xl shadow-lg shadow-purple-500/30 text-center col-span-2">
                    <div class="text-4xl font-black text-white mb-2">390</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-purple-100 mb-1">Points</div>
                    <div class="text-sm font-bold text-white">Online Benchmarks</div>
                </div>
            </div>

            <!-- Core System Section -->
            <div class="mb-12">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-1 rounded-3xl mb-12">
                    <div class="bg-white dark:bg-[#121212] rounded-[22px] p-8">
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4">🧩 Core System — 940 Points</h2>
                        <p class="text-slate-600 dark:text-slate-400">Pure hardware and measurable features, evaluated
                            independently of subjective testing.</p>
                    </div>
                </div>
            </div>

            <!-- Detailed Methodologies -->
            <div class="space-y-12">

                <!-- Section 1: Sensor & Optics -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Sensor & Optics (240 Points)</h2>
                        <p class="text-sm text-slate-500 mt-2">Per-camera evaluation with weighted fusion</p>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Sensor Size (40 pts/camera)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>≥1/1.4"</span> <span
                                            class="font-mono text-purple-600 font-bold">+40</span></li>
                                    <li class="flex justify-between"><span>1/1.7–1/1.5"</span> <span
                                            class="font-mono text-purple-600 font-bold">+32</span></li>
                                    <li class="flex justify-between"><span>1/2–1/1.8"</span> <span
                                            class="font-mono text-purple-600 font-bold">+24</span></li>
                                    <li class="flex justify-between"><span>1/2.8–1/2.5"</span> <span
                                            class="font-mono text-purple-600 font-bold">+16</span></li>
                                    <li class="flex justify-between"><span>≤1/3"</span> <span
                                            class="font-mono text-purple-600 font-bold">+8</span></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Pixel Size (25 pts/camera)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>≥1.4µm</span> <span
                                            class="font-mono text-purple-600 font-bold">+25</span></li>
                                    <li class="flex justify-between"><span>1.1–1.3µm</span> <span
                                            class="font-mono text-purple-600 font-bold">+20</span></li>
                                    <li class="flex justify-between"><span>0.9–1.0µm</span> <span
                                            class="font-mono text-purple-600 font-bold">+15</span></li>
                                    <li class="flex justify-between"><span>0.7–0.8µm</span> <span
                                            class="font-mono text-purple-600 font-bold">+10</span></li>
                                    <li class="flex justify-between"><span>≤0.6µm</span> <span
                                            class="font-mono text-purple-600 font-bold">+5</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Aperture (25 pts/camera)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>≤f/1.6</span> <span
                                            class="font-mono text-purple-600 font-bold">+25</span></li>
                                    <li class="flex justify-between"><span>f/1.7–1.9</span> <span
                                            class="font-mono text-purple-600 font-bold">+20</span></li>
                                    <li class="flex justify-between"><span>f/2.0–2.3</span> <span
                                            class="font-mono text-purple-600 font-bold">+15</span></li>
                                    <li class="flex justify-between"><span>f/2.4–2.7</span> <span
                                            class="font-mono text-purple-600 font-bold">+10</span></li>
                                    <li class="flex justify-between"><span>≥f/2.8</span> <span
                                            class="font-mono text-purple-600 font-bold">+5</span></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Optics Type (10 pts/camera)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>Multi-element premium</span> <span
                                            class="font-mono text-purple-600 font-bold">+10</span></li>
                                    <li class="flex justify-between"><span>Periscope optics</span> <span
                                            class="font-mono text-purple-600 font-bold">+8</span></li>
                                    <li class="flex justify-between"><span>Aspheric lens</span> <span
                                            class="font-mono text-purple-600 font-bold">+6</span></li>
                                    <li class="flex justify-between"><span>Standard lens</span> <span
                                            class="font-mono text-purple-600 font-bold">+4</span></li>
                                    <li class="flex justify-between"><span>Fixed basic lens</span> <span
                                            class="font-mono text-purple-600 font-bold">+2</span></li>
                                </ul>
                            </div>
                        </div>
                        <div
                            class="rounded-2xl border border-purple-200 dark:border-purple-900/30 p-5 bg-purple-50 dark:bg-purple-900/10 mt-6">
                            <h4 class="font-bold text-slate-900 dark:text-white mb-2">Multi-Camera Fusion Weighting</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                Main × 0.5 | Telephoto × 0.3 | Ultra-wide × 0.2 | Additional cameras distribute remaining
                                0.1
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Resolution & Binning -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. Resolution & Binning (90 Points)
                        </h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">≥200MP (multi-bin)</span>
                                <span class="font-mono text-purple-600 font-bold">+90</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">108MP (tetra-bin)</span>
                                <span class="font-mono text-purple-600 font-bold">+80</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">50–64MP (quad-bin)</span>
                                <span class="font-mono text-purple-600 font-bold">+65</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">48MP (quad-bin)</span>
                                <span class="font-mono text-purple-600 font-bold">+45</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">16–24MP</span>
                                <span class="font-mono text-purple-600 font-bold">+25</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">≤12MP (no binning)</span>
                                <span class="font-mono text-purple-600 font-bold">+10</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Focus & Stability -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">3. Focus 3. Focus & Stability (80 Points) Stability (200 Points)</h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Autofocus (100 pts)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>Dual-Pixel + Laser</span> <span
                                            class="font-mono text-purple-600 font-bold">+40</span></li>
                                    <li class="flex justify-between"><span>Dual-Pixel AF</span> <span
                                            class="font-mono text-purple-600 font-bold">+32</span></li>
                                    <li class="flex justify-between"><span>Multi-PDAF</span> <span
                                            class="font-mono text-purple-600 font-bold">+24</span></li>
                                    <li class="flex justify-between"><span>PDAF</span> <span
                                            class="font-mono text-purple-600 font-bold">+16</span></li>
                                    <li class="flex justify-between"><span>Contrast AF</span> <span
                                            class="font-mono text-purple-600 font-bold">+8</span></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Stabilization (100 pts)</h3>
                                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li class="flex justify-between"><span>OIS + gyro-EIS</span> <span
                                            class="font-mono text-purple-600 font-bold">+40</span></li>
                                    <li class="flex justify-between"><span>OIS + EIS</span> <span
                                            class="font-mono text-purple-600 font-bold">+32</span></li>
                                    <li class="flex justify-between"><span>OIS</span> <span
                                            class="font-mono text-purple-600 font-bold">+25</span></li>
                                    <li class="flex justify-between"><span>EIS</span> <span
                                            class="font-mono text-purple-600 font-bold">+15</span></li>
                                    <li class="flex justify-between"><span>None</span> <span
                                            class="font-mono text-slate-400">+5</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Video System -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">4. Video System (200 Points)</h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div
                                class="flex flex-col items-center p-6 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white">
                                <span class="text-3xl font-black mb-1">200</span>
                                <span class="text-xs font-bold opacity-90">8K60</span>
                            </div>
                            <div class="flex flex-col items-center p-6 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-3xl font-black text-slate-900 dark:text-white mb-1">90</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">8K30</span>
                            </div>
                            <div class="flex flex-col items-center p-6 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-3xl font-black text-slate-900 dark:text-white mb-1">70</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">6K</span>
                            </div>
                            <div class="flex flex-col items-center p-6 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-3xl font-black text-slate-900 dark:text-white mb-1">200</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">4K60</span>
                            </div>
                            <div class="flex flex-col items-center p-6 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-3xl font-black text-slate-900 dark:text-white mb-1">100</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">4K30</span>
                            </div>
                            <div class="flex flex-col items-center p-6 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-3xl font-black text-slate-900 dark:text-white mb-1">10</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">1080p</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Multi-Camera Fusion -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">5. Multi-Camera Fusion (200 Points)
                        </h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center p-4 rounded-xl bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-200 dark:border-purple-900/30">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">AI fusion
                                    multi-sensor</span>
                                <span class="font-mono text-purple-600 font-bold text-lg">+50</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Quad camera</span>
                                <span class="font-mono text-purple-600 font-bold">+40</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Triple camera</span>
                                <span class="font-mono text-purple-600 font-bold">+30</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Dual camera</span>
                                <span class="font-mono text-purple-600 font-bold">+15</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Single camera</span>
                                <span class="font-mono text-purple-600 font-bold">+5</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 6: Special Features -->
                <section
                    class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-white/5">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">6. Special Features (100 Points)</h2>
                        <p class="text-sm text-slate-500 mt-2">Each feature awards +5 points (cumulative)</p>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">📸</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">RAW Capture</span>
                            </div>
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">🌅</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Multi-frame HDR</span>
                            </div>
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">🌈</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Spectrum Sensor</span>
                            </div>
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">🌙</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Night Mode</span>
                            </div>
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">🎬</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Pro Video (LOG)</span>
                            </div>
                            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                                <span class="text-2xl">📏</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Depth/LiDAR</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Online Benchmark System -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-1 rounded-3xl">
                    <div class="bg-white dark:bg-[#121212] rounded-[22px] p-8">
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4">🌐 Online Benchmark System —
                            390 Points</h2>
                        <p class="text-slate-600 dark:text-slate-400 mb-6">Professional camera testing scores from
                            established review platforms</p>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-5 rounded-xl bg-slate-50 dark:bg-white/5">
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">DxOMark Camera Score</div>
                                    <div class="text-xs text-slate-500">Industry-leading camera testing lab</div>
                                </div>
                                <span class="font-mono text-purple-600 font-bold text-2xl">180</span>
                            </div>
                            <div class="flex justify-between items-center p-5 rounded-xl bg-slate-50 dark:bg-white/5">
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">PhoneArena Camera Score</div>
                                    <div class="text-xs text-slate-500">Comprehensive real-world testing</div>
                                </div>
                                <span class="font-mono text-purple-600 font-bold text-2xl">130</span>
                            </div>
                            <div class="flex justify-between items-center p-5 rounded-xl bg-slate-50 dark:bg-white/5">
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Other Benchmarks</div>
                                    <div class="text-xs text-slate-500">GSMArena, AnandTech, etc.</div>
                                </div>
                                <span class="font-mono text-purple-600 font-bold text-2xl">200</span>
                            </div>
                        </div>

                        <div
                            class="mt-6 p-4 rounded-xl bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/30">
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <strong>Note:</strong> If benchmark data is unavailable → NULL → 0 points for that source
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Final Formula -->
                <section
                    class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-white/10 dark:to-white/5 rounded-3xl shadow-xl overflow-hidden border border-slate-700 dark:border-white/10">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-white mb-6">🧮 Final Formula</h2>
                        <div class="bg-black/30 rounded-2xl p-6 font-mono text-sm">
                            <div class="text-purple-300 mb-2">FINAL CMS-1330 SCORE =</div>
                            <div class="text-white pl-8 mb-1">CORE SYSTEM (max 940)</div>
                            <div class="text-white pl-8 mb-1">+ ONLINE BENCHMARK SYSTEM (max 390)</div>
                            <div class="text-purple-300 mt-2">= 1330 points</div>
                        </div>
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
                            class="flex justify-between items-center p-4 rounded-xl bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30">
                            <span class="font-bold text-slate-900 dark:text-white">900–1000</span>
                            <span class="text-sm font-bold text-yellow-700 dark:text-yellow-400">Imaging Excellence</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">800–899</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Professional Grade</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">700–799</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Enthusiast Grade</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">600–699</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Mainstream Plus</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">500–599</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Mainstream</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">400–499</span>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Budget Plus</span>
                        </div>
                        <div class="flex justify-between items-center p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                            <span class="font-bold text-slate-900 dark:text-white">
                                <400< /span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Entry Level</span>
                        </div>
                    </div>
                </section>

                <div class="text-center pt-8">
                    <p class="text-slate-500 mb-6">Full details available in our documentation.</p>
                    <a href="{{ route('phones.rankings') }}"
                        class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold rounded-xl hover:scale-105 transition-transform shadow-lg shadow-purple-500/30">
                        Check the Rankings →
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
