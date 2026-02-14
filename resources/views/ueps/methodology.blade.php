@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-teal-500 selection:text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-800 dark:text-teal-300 text-sm font-bold mb-4 border border-teal-200 dark:border-teal-800">
                UEPS 4.0
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-6">
                Ultra-Extensive Phone Scoring System (UEPS-40)
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                A comprehensive 200-point system designed to evaluate smartphones based on real-world usage, covering every aspect from build quality to connectivity.
            </p>
        </div>

        <!-- Score Breakdown Visual -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">30</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Build & Durability</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">40</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Display Tech</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">30</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Processing</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">30</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Power & Battery</div>
            </div>
             <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">30</div>
                 <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Camera System</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">25</div>
                 <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Connectivity</div>
            </div>
             <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center col-span-2 md:col-span-2 hover:border-teal-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-teal-500 transition-colors">15</div>
                 <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Points</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Audio & Extras</div>
            </div>
        </div>

        <!-- Detailed Methodologies -->
        <div class="space-y-12">
            
            <!-- Section A -->
            <section class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">A. Build & Durability (30 Points)</h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Frame Material</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>Titanium / Stainless Steel</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span>Aluminum / Metal</span> <span class="font-mono text-teal-600 font-bold">+3</span></li>
                                <li class="flex justify-between"><span>Plastic / Other</span> <span class="font-mono text-slate-400">0</span></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Back Material</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>Glass / Ceramic</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span>Eco Leather / High-grade Fiber</span> <span class="font-mono text-teal-600 font-bold">+3</span></li>
                                <li class="flex justify-between"><span>Plastic / Other</span> <span class="font-mono text-teal-600 font-bold">+1</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

             <!-- Section B -->
            <section class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">B. Display Tech (40 Points)</h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                         <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Panel Technology</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>LTPO OLED/AMOLED</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span>Standard OLED/AMOLED</span> <span class="font-mono text-teal-600 font-bold">+3</span></li>
                                <li class="flex justify-between"><span>LCD</span> <span class="font-mono text-teal-600 font-bold">+1</span></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Peak Brightness</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>>4000 nits</span> <span class="font-mono text-teal-600 font-bold">+10</span></li>
                                <li class="flex justify-between"><span>2000-3999 nits</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span><2000 nits</span> <span class="font-mono text-slate-400">0</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

             <!-- Section C -->
            <section class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">C. Processing & Memory (30 Points)</h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                         <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Processor Tier</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>Latest Flagship (e.g., 8 Elite)</span> <span class="font-mono text-teal-600 font-bold">+10</span></li>
                                <li class="flex justify-between"><span>Previous Gen Flagship (e.g., 8 Gen 3)</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2">Storage & RAM</h3>
                            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex justify-between"><span>UFS 4.0</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span>16GB/24GB RAM Option</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                                <li class="flex justify-between"><span>SD Card Slot</span> <span class="font-mono text-teal-600 font-bold">+5</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

             <!-- More sections as needed... -->
             <div class="text-center pt-8">
                 <p class="text-slate-500 mb-6">Full details available in our documentation.</p>
                 <a href="{{ route('phones.rankings') }}" class="inline-flex items-center justify-center px-8 py-3 bg-slate-900 dark:bg-white text-white dark:text-black font-bold rounded-xl hover:scale-105 transition-transform">
                     Check the Rankings ->
                 </a>
             </div>

        </div>
    </div>
</div>
@endsection
