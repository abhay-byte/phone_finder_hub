@extends('layouts.app')

@section('content')
<div class="bg-[#F5F5F7] dark:bg-black min-h-screen py-8 md:py-16 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb / Back -->
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('phones.index') }}" class="hover:text-black dark:hover:text-white transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Phones
            </a>
        </nav>

        <!-- Hero Section: Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left: Image (Sticky on Desktop) -->
            <div class="lg:col-span-7 flex justify-center lg:sticky lg:top-24">
                <div class="relative w-full max-w-lg aspect-[4/5] flex items-center justify-center p-8 bg-white dark:bg-[#121212] rounded-[3rem] shadow-2xl ring-1 ring-black/5 dark:ring-white/10">
                     <div class="absolute inset-0 bg-gradient-to-tr from-gray-100 to-transparent dark:from-white/5 dark:to-transparent rounded-[3rem] pointer-events-none"></div>
                    @if($phone->image_url)
                        <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="relative z-10 w-full h-full object-contain filter drop-shadow-2xl hover:scale-105 transition-transform duration-700 ease-out">
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-300 dark:text-gray-700">
                            <svg class="w-32 h-32 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-medium">No Image Available</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Info & Bento Grid -->
            <div class="lg:col-span-5 space-y-8">
                
                <!-- Title & Meta -->
                <div class="text-center lg:text-left space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/5 dark:bg-white/10 text-xs font-bold uppercase tracking-wider text-black dark:text-white">
                        {{ $phone->brand }}
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black tracking-tight text-black dark:text-white leading-tight">
                        {{ $phone->name }}
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 font-medium">
                        {{ $phone->model_variant }}
                    </p>
                </div>

                <!-- Price & Value Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1 p-6 bg-black dark:bg-white rounded-3xl text-white dark:text-black shadow-xl flex flex-col justify-between h-40 group hover:scale-[1.02] transition-transform">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold opacity-70 uppercase tracking-widest">Value Score</p>
                            <svg class="w-6 h-6 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                        </div>
                        <div>
                            <span class="text-5xl font-black tracking-tighter">{{ $phone->value_score }}</span>
                            <span class="text-sm font-medium opacity-60 ml-1">pts/₹1k</span>
                        </div>
                    </div>

                    <div class="col-span-2 sm:col-span-1 p-6 bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-100 dark:border-white/10 shadow-lg flex flex-col justify-between h-40 group hover:scale-[1.02] transition-transform">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Price</p>
                             <div class="bg-green-100 dark:bg-green-900/30 p-1.5 rounded-full text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                        <div class="flex items-end gap-1">
                             <span class="text-3xl font-bold text-black dark:text-white">₹{{ number_format($phone->price) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Specs Bento -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Chipset -->
                    @if($phone->platform)
                    <div class="col-span-2 p-6 bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-100 dark:border-white/10 flex items-center gap-4">
                         <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Powerhouse</p>
                            <p class="text-lg font-bold text-black dark:text-white leading-tight">{{ $phone->platform->chipset }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Display -->
                    @if($phone->body)
                    <div class="p-5 bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-100 dark:border-white/10">
                        <svg class="w-6 h-6 mb-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        <p class="text-lg font-bold text-black dark:text-white leading-none mb-1">{{ $phone->body->display_size }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $phone->body->display_type }}</p>
                    </div>
                    @endif

                    <!-- Battery -->
                    @if($phone->battery)
                     <div class="p-5 bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-100 dark:border-white/10">
                        <svg class="w-6 h-6 mb-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <p class="text-lg font-bold text-black dark:text-white leading-none mb-1">{{ $phone->battery->battery_type }}</p>
                         <p class="text-xs text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap overflow-hidden text-ellipsis">{{ $phone->battery->charging_wired }}</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Detailed Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pt-8">
            
            <!-- Benchmarks (Spans 2 cols on wide) -->
            <div class="md:col-span-2 bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 md:p-10 shadow-sm ring-1 ring-gray-100 dark:ring-white/5">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-black dark:text-white">Performance Metrics</h3>
                    <div class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase">Benchmarks</div>
                </div>
                
                 @if($phone->benchmarks)
                <div class="space-y-8">
                    <!-- AnTuTu Bar -->
                    <div class="group">
                        <div class="flex justify-between mb-2">
                             <span class="font-semibold text-gray-600 dark:text-gray-300">AnTuTu v10</span>
                             <span class="font-bold text-black dark:text-white">{{ number_format($phone->benchmarks->antutu_score) }}</span>
                        </div>
                        <div class="h-6 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                             <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full w-0 transition-all duration-1000 ease-out group-hover:w-[{{ min(($phone->benchmarks->antutu_score / 3000000) * 100, 100) }}%]" style="width: {{ min(($phone->benchmarks->antutu_score / 3000000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                         <!-- Geekbench -->
                         <div class="group">
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-600 dark:text-gray-300">Geekbench Multi</span>
                                <span class="font-bold text-black dark:text-white">{{ number_format($phone->benchmarks->geekbench_multi) }}</span>
                            </div>
                            <div class="h-4 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full w-0 transition-all duration-1000 ease-out group-hover:w-[{{ min(($phone->benchmarks->geekbench_multi / 10000) * 100, 100) }}%]" style="width: {{ min(($phone->benchmarks->geekbench_multi / 10000) * 100, 100) }}%"></div>
                            </div>
                        </div>
                         <!-- 3DMark -->
                         <div class="group">
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-600 dark:text-gray-300">3DMark Extreme</span>
                                <span class="font-bold text-black dark:text-white">{{ number_format($phone->benchmarks->dmark_wild_life_extreme ?? 0) }}</span>
                            </div>
                            <div class="h-4 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500 rounded-full w-0 transition-all duration-1000 ease-out group-hover:w-[{{ min(($phone->benchmarks->dmark_wild_life_extreme / 8000) * 100, 100) }}%]" style="width: {{ min(($phone->benchmarks->dmark_wild_life_extreme / 8000) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Cameras -->
             @if($phone->camera)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 flex flex-col justify-center">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Camera System</h3>
                    <p class="text-sm text-gray-500">Professional Optics</p>
                </div>
                
                <div class="space-y-6">
                    <div class="pl-4 border-l-2 border-indigo-500">
                        <p class="text-xs font-bold text-indigo-500 uppercase mb-1">Main Module</p>
                         <p class="text-sm font-medium text-black dark:text-white leading-relaxed">{{ $phone->camera->main_camera_specs }}</p>
                    </div>
                     <div class="pl-4 border-l-2 border-rose-500">
                        <p class="text-xs font-bold text-rose-500 uppercase mb-1">Selfie</p>
                         <p class="text-sm font-medium text-black dark:text-white">{{ $phone->camera->selfie_camera_specs }}</p>
                    </div>
                     <div class="pl-4 border-l-2 border-gray-300 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Video</p>
                         <p class="text-sm font-medium text-black dark:text-white">{{ $phone->camera->main_video_capabilities }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Platform Details -->
             @if($phone->platform)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Core Architecture</h3>
                    <p class="text-sm text-gray-500">Processing & Memory</p>
                </div>
                <dl class="space-y-4">
                    <div class="pb-4 border-b border-gray-100 dark:border-white/5">
                        <dt class="text-xs font-bold text-gray-400 uppercase">OS</dt>
                        <dd class="text-base font-semibold text-black dark:text-white">{{ $phone->platform->os }}</dd>
                    </div>
                     <div class="pb-4 border-b border-gray-100 dark:border-white/5">
                        <dt class="text-xs font-bold text-gray-400 uppercase">CPU</dt>
                        <dd class="text-sm font-medium text-black dark:text-white leading-snug mt-1">{{ $phone->platform->cpu }}</dd>
                    </div>
                    <div class="pb-4 border-b border-gray-100 dark:border-white/5">
                        <dt class="text-xs font-bold text-gray-400 uppercase">GPU</dt>
                        <dd class="text-base font-semibold text-black dark:text-white">{{ $phone->platform->gpu }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase">Config</dt>
                        <dd class="text-base font-semibold text-black dark:text-white">{{ $phone->platform->ram }} / {{ $phone->platform->internal_storage }}</dd>
                    </div>
                </dl>
            </div>
             @endif
            
             <!-- Connectivity -->
            @if($phone->connectivity)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 md:col-span-2 xl:col-span-1">
                 <div class="mb-6">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Connectivity</h3>
                    <p class="text-sm text-gray-500">Network & Sensors</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                        <dt class="text-xs text-gray-500 uppercase">WLAN</dt>
                        <dd class="text-xs font-medium text-black dark:text-white truncate" title="{{ $phone->connectivity->wlan }}">{{ $phone->connectivity->wlan }}</dd>
                    </div>
                     <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                        <dt class="text-xs text-gray-500 uppercase">Bluetooth</dt>
                        <dd class="text-xs font-medium text-black dark:text-white">{{ $phone->connectivity->bluetooth }}</dd>
                    </div>
                     <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">Features</dt>
                        <dd class="text-xs font-medium text-black dark:text-white">NFC: {{ $phone->connectivity->nfc }} • IR: {{ $phone->connectivity->infrared }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">Sensors</dt>
                        <dd class="text-xs font-medium text-black dark:text-white text-wrap leading-snug">{{ $phone->connectivity->sensors }}</dd>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection
