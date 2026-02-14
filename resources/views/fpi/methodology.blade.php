@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-blue-500 selection:text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-bold mb-4 border border-blue-200 dark:border-blue-800">
                FPI Score
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-6">
                Final Performance Index
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                A weighted performance metric that normalizes benchmark scores to reflect real-world power, stability, and efficiency.
            </p>
        </div>

        <!-- Formula Visual -->
        <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 mb-16 text-center">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-6">The Formula</h3>
            <div class="inline-flex items-center flex-wrap justify-center gap-4 text-lg md:text-2xl font-bold text-slate-900 dark:text-white font-mono">
                <span>(AnTuTu v11 <span class="text-blue-500">× 40%</span>)</span>
                <span>+</span>
                <span>(Geekbench Multi <span class="text-blue-500">× 25%</span>)</span>
                <span>+</span>
                <span>(Geekbench Single <span class="text-blue-500">× 15%</span>)</span>
                <span>+</span>
                <span>(3DMark <span class="text-blue-500">× 20%</span>)</span>
            </div>
        </div>

        <!-- Weight Breakdown -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-blue-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-blue-500 transition-colors">40%</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">AnTuTu v11</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-blue-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-blue-500 transition-colors">25%</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Geekbench Multi</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-blue-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-blue-500 transition-colors">20%</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">3DMark Extreme</div>
            </div>
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5 text-center hover:border-blue-500/30 transition-colors group">
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-2 group-hover:text-blue-500 transition-colors">15%</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Weight</div>
                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Geekbench Single</div>
            </div>
        </div>

        <!-- Detailed Methodologies -->
        <div class="space-y-12">
            
            <section class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Why Normalization?</h2>
                </div>
                <div class="p-8 text-slate-600 dark:text-slate-400 leading-relaxed space-y-4">
                    <p>
                        Raw benchmark scores can be misleading because they use vastly different scales (e.g., AnTuTu v11 is in millions, while Geekbench is in thousands).
                    </p>
                    <p>
                        FPI normalizes these scores using a "Percentage of Best" method. The highest scoring device in the database for a specific benchmark (e.g., Snapdragon 8 Elite) sets the standard (100%), and other devices are scored relative to it.
                    </p>
                </div>
            </section>

             <div class="text-center pt-8">
                 <a href="{{ route('phones.rankings', ['tab' => 'performance']) }}" class="inline-flex items-center justify-center px-8 py-3 bg-slate-900 dark:bg-white text-white dark:text-black font-bold rounded-xl hover:scale-105 transition-transform">
                     View Performance Rankings ->
                 </a>
             </div>

        </div>
    </div>
</div>
@endsection
