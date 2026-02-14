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
                    <!-- Performance Index (FPI) -->
                    @php $fpi = $phone->calculateFPI(); @endphp
                    <div class="col-span-2 sm:col-span-1 p-6 bg-black dark:bg-white rounded-3xl text-white dark:text-black shadow-xl flex flex-col justify-between h-40 group hover:scale-[1.02] transition-transform">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold opacity-70 uppercase tracking-widest">Performance Index</p>
                            <svg class="w-6 h-6 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2.05v3.03c3.39.49 6 3.39 6 6.92 0 .9-.18 1.75-.5 2.54l2.63 1.53c.56-1.24.87-2.6.87-4.07 0-5.19-3.95-9.45-8.98-9.95zM7.5 12c0 2.48 2.02 4.5 4.5 4.5s4.5-2.02 4.5-4.5-2.02-4.5-4.5-4.5-4.5 2.02-4.5 4.5zM11 2.05v3.03c-3 .5-5.38 2.89-5.88 5.89h-3.03c.51-4.62 4.13-8.24 8.75-8.75zM4.12 13.06L1.49 14.59c.7 1.72 1.88 3.19 3.35 4.29l1.85-2.31c-.96-.75-1.74-1.72-2.22-2.87-.14-.33-.29-.68-.35-1.04zM13 21.95v-3.03c2.61-.43 4.75-2.32 5.56-4.78l2.84.97c-1.19 3.63-4.35 6.4-8.38 6.84z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-black tracking-tighter">{{ is_array($fpi) ? $fpi['total'] : 0 }}</span>
                                <span class="text-lg font-bold opacity-50">/100</span>
                            </div>
                            <span class="text-xs font-medium opacity-60 block mt-1">Weighted Score</span>
                        </div>
                    </div>

                    <div class="col-span-2 sm:col-span-1 p-6 bg-gray-100 dark:bg-gray-800 rounded-3xl text-black dark:text-white shadow-sm flex flex-col justify-between h-40 group hover:scale-[1.02] transition-transform">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold opacity-50 uppercase tracking-widest text-gray-500 dark:text-gray-400">Value Score</p>
                            <svg class="w-6 h-6 opacity-30 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                        </div>
                        <div>
                            <span class="text-5xl font-black tracking-tighter text-gray-400 dark:text-gray-500">{{ $phone->value_score }}</span>
                            <span class="text-sm font-medium opacity-40 ml-1 text-gray-500">FPI/₹10k</span>
                        </div>
                    </div>

                    <!-- Buy Links (Replacing Price Card) -->
                    <div class="col-span-2 sm:col-span-1 grid grid-rows-2 gap-4 h-40">
                        @if($phone->amazon_url)
                        <a href="{{ $phone->amazon_url }}" target="_blank" class="flex items-center justify-between px-6 bg-[#FF9900] hover:bg-[#ff8c00] text-white rounded-3xl font-bold shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 transition-all hover:-translate-y-1 group">
                            <div class="flex items-center gap-3">
                                <div class="bg-white p-1.5 rounded-full shrink-0">
                                    <img src="{{ asset('assets/amazon-icon.png') }}" alt="Amazon" class="w-5 h-5 object-contain">
                                </div>
                                <div class="flex flex-col items-start leading-none">
                                    <span class="text-xs font-medium opacity-90 mb-0.5">Buy on</span>
                                    <span class="text-sm font-bold">Amazon</span>
                                </div>
                            </div>
                            @if($phone->amazon_price)
                            <span class="text-base font-bold">₹{{ number_format($phone->amazon_price) }}</span>
                            @endif
                        </a>
                        @endif
                        
                        @if($phone->flipkart_url)
                        <a href="{{ $phone->flipkart_url }}" target="_blank" class="flex items-center justify-between px-6 bg-[#2874F0] hover:bg-[#1e65d6] text-white rounded-3xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all hover:-translate-y-1 group">
                             <div class="flex items-center gap-3">
                                <div class="bg-white p-1.5 rounded-full shrink-0">
                                    <img src="{{ asset('assets/flipkart-icon.png') }}" alt="Flipkart" class="w-5 h-5 object-contain">
                                </div>
                                <div class="flex flex-col items-start leading-none">
                                    <span class="text-xs font-medium opacity-90 mb-0.5">Buy on</span>
                                    <span class="text-sm font-bold">Flipkart</span>
                                </div>
                             </div>
                             @if($phone->flipkart_price)
                            <span class="text-base font-bold">₹{{ number_format($phone->flipkart_price) }}</span>
                            @endif
                        </a>
                        @endif
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
                <!-- UEPS-40 Enthusiast Score -->
                <div class="mt-6 bg-gradient-to-br from-gray-900 to-black dark:from-white dark:to-gray-100 text-white dark:text-black rounded-3xl p-6 shadow-2xl relative overflow-hidden group">
                     <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-purple-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                     <div class="relative z-10">
                        <div class="flex flex-col md:flex-row gap-6 items-center mb-6">
                            <div class="flex-1 text-center md:text-left">
                                 <h3 class="text-lg font-bold uppercase tracking-widest opacity-80 mb-1">UEPS-40 Score</h3>
                                 <p class="text-xs opacity-60 mb-4">Ultra-Extensive Phone Scoring System</p>
                                 <div class="flex items-baseline justify-center md:justify-start gap-2">
                                    <span class="text-6xl font-black tracking-tighter">{{ $phone->ueps_score['total_score'] }}</span>
                                    <span class="text-xl font-bold opacity-50">/200</span>
                                 </div>
                                 <div class="inline-block mt-2 px-4 py-1 rounded-full bg-white/10 dark:bg-black/10 backdrop-blur-md border border-white/20 dark:border-black/20 text-sm font-bold">
                                    {{ $phone->ueps_score['percentage'] }}% &bull; Enthusiast Grade
                                 </div>
                            </div>
                            
                            <!-- Radial Progress / Visual -->
                            <div class="relative w-32 h-32 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/10 dark:text-black/10" />
                                    <circle cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="377" stroke-dashoffset="{{ 377 - (377 * $phone->ueps_score['percentage'] / 100) }}" class="text-purple-500 transition-all duration-1000 ease-out" />
                                </svg> 
                                <span class="absolute text-2xl font-black">{{ $phone->ueps_score['total_score'] }}</span>
                            </div>
                        </div>

                        <!-- Detailed Breakdown -->
                        <div class="space-y-3">
                            @foreach($phone->ueps_score['breakdown'] as $category => $data)
                            <details class="group/details bg-white/5 dark:bg-black/5 rounded-xl border border-white/10 dark:border-black/10 overflow-hidden">
                                <summary class="cursor-pointer p-4 flex items-center justify-between hover:bg-white/5 dark:hover:bg-black/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-sm">{{ $category }}</span>
                                        <span class="text-xs opacity-60">({{ $data['score'] }}/{{ $data['max'] }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-1.5 bg-white/10 dark:bg-black/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ ($data['score'] / $data['max']) * 100 }}%"></div>
                                        </div>
                                        <svg class="w-4 h-4 opacity-50 group-open/details:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </summary>
                                <div class="p-4 pt-0 text-xs opacity-80 border-t border-white/5 dark:border-black/5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3">
                                        @foreach($data['details'] as $detail)
                                        <div class="flex justify-between border-b border-dashed border-white/10 dark:border-black/10 pb-1 last:border-0">
                                            <span>{{ $detail['criterion'] }}</span>
                                            <span class="font-medium {{ $detail['points'] > 0 ? 'text-green-400 dark:text-green-600' : 'text-red-400 dark:text-red-600' }}">{{ $detail['points'] > 0 ? '+' . $detail['points'] : '0' }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                            @endforeach
                        </div>
                     </div>
                </div>
                <!-- Enthusiast Metrics (Concise) -->
                @if($phone->platform)
                <div class="mt-4 p-5 bg-white dark:bg-[#1A1A1A] rounded-3xl border border-gray-100 dark:border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                         <div class="p-2.5 rounded-full bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-black dark:text-white">Developer Ready</p>
                            <p class="text-xs text-gray-500 font-medium">Enthusiast Features</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        @if($phone->platform->bootloader_unlockable)
                        <span class="px-2.5 py-1 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-bold border border-green-100 dark:border-green-500/20" title="Bootloader Unlockable">BL</span>
                        @endif
                        @if($phone->platform->turnip_support)
                        <span class="px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 text-xs font-bold border border-purple-100 dark:border-purple-500/20" title="Turnip Driver Support">Turnip</span>
                        @endif
                         <span class="px-2.5 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs font-bold border border-gray-200 dark:border-gray-700" title="AOSP Aesthetics Score">{{ $phone->platform->aosp_aesthetics_score }}/10</span>
                    </div>
                </div>
                @endif

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
                             <span class="font-semibold text-gray-600 dark:text-gray-300">AnTuTu v11</span>
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
                                <div class="text-right">
                                    <span class="font-bold text-black dark:text-white block leading-none">{{ number_format($phone->benchmarks->geekbench_multi) }}</span>
                                    <span class="text-xs text-gray-400 font-medium">Single: {{ number_format($phone->benchmarks->geekbench_single) }}</span>
                                </div>
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
                         <!-- Battery Endurance -->
                         <div class="group sm:col-span-2">
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-600 dark:text-gray-300">Battery Endurance</span>
                                <span class="font-bold text-black dark:text-white">{{ $phone->benchmarks->battery_endurance_hours ?? 'N/A' }}h</span>
                            </div>
                            <div class="h-4 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full w-0 transition-all duration-1000 ease-out group-hover:w-[{{ min(($phone->benchmarks->battery_endurance_hours / 24) * 100, 100) }}%]" style="width: {{ min(($phone->benchmarks->battery_endurance_hours / 24) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Cameras -->
             @if($phone->camera)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 flex flex-col">
                <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Camera System</h3>
                    <p class="text-sm text-gray-500">Professional Optics</p>
                </div>
                
                <div class="space-y-6">
                    <div class="pl-4 border-l-2 border-indigo-500">
                        <p class="text-xs font-bold text-indigo-500 uppercase mb-1">Main Module</p>
                         <p class="text-sm font-medium text-black dark:text-white leading-relaxed">{{ $phone->camera->main_camera_specs }}</p>
                         <p class="text-xs text-gray-500 mt-1">{{ $phone->camera->main_camera_features }}</p>
                    </div>
                     <div class="pl-4 border-l-2 border-rose-500">
                        <p class="text-xs font-bold text-rose-500 uppercase mb-1">Selfie</p>
                         <p class="text-sm font-medium text-black dark:text-white">{{ $phone->camera->selfie_camera_specs }}</p>
                         <p class="text-xs text-gray-500 mt-1">{{ $phone->camera->selfie_camera_features }}</p>
                    </div>
                     <div class="pl-4 border-l-2 border-gray-300 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Video Capabilities</p>
                         <div class="space-y-1">
                             <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Rear</span>
                                <span class="text-xs font-medium text-black dark:text-white">{{ $phone->camera->main_video_capabilities }}</span>
                             </div>
                             <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Front</span>
                                <span class="text-xs font-medium text-black dark:text-white">{{ $phone->camera->selfie_video_capabilities }}</span>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Platform Details -->
             @if($phone->platform)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5">
                <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Core Architecture</h3>
                    <p class="text-sm text-gray-500">Processing & Memory</p>
                </div>
                <dl class="space-y-4">
                    <div class="pb-4 border-b border-gray-100 dark:border-white/5">
                        <dt class="text-xs font-bold text-gray-400 uppercase">OS</dt>
                        <dd class="text-base font-semibold text-black dark:text-white">{{ $phone->platform->os }}</dd>
                    </div>
                     <div class="pb-4 border-b border-gray-100 dark:border-white/5">
                        <dt class="text-xs font-bold text-gray-400 uppercase">Chipset</dt>
                        <dd class="text-base font-semibold text-black dark:text-white">{{ $phone->platform->chipset }}</dd>
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
                        <dt class="text-xs font-bold text-gray-400 uppercase">Memory</dt>
                        <div class="flex justify-between mt-1">
                            <span class="text-sm font-medium text-black dark:text-white">{{ $phone->platform->ram }} RAM</span>
                            <span class="text-sm font-medium text-black dark:text-white">{{ $phone->platform->internal_storage }}</span>
                        </div>
                        <div class="flex justify-between mt-1 text-xs text-gray-500">
                             <span>Type: {{ $phone->platform->storage_type }}</span>
                             <span>Card Slot: {{ $phone->platform->memory_card_slot }}</span>
                        </div>
                    </div>
                </dl>
            </div>
             @endif

              <!-- Display & Body (New Section) -->
             @if($phone->body)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 md:col-span-2 xl:col-span-1">
                <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Display & Design</h3>
                    <p class="text-sm text-gray-500">Form Factor & Visuals</p>
                </div>
                <div class="space-y-6">
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Display</dt>
                        <dd class="text-sm font-medium text-black dark:text-white">{{ $phone->body->display_size }} {{ $phone->body->display_type }}</dd>
                        <dd class="text-xs text-gray-500 mt-1">{{ $phone->body->display_resolution }} • {{ $phone->body->display_features }}</dd>
                        <dd class="text-xs text-gray-500 mt-1">Protection: {{ $phone->body->display_protection }}</dd>
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                         <div>
                            <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Body</dt>
                            <dd class="text-sm font-medium text-black dark:text-white">{{ $phone->body->weight }}</dd>
                             <dd class="text-xs text-gray-500">{{ $phone->body->dimensions }}</dd>
                         </div>
                         <div>
                            <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Build</dt>
                            <dd class="text-xs text-gray-500 leading-snug">{{ $phone->body->build_material }}</dd>
                         </div>
                     </div>
                      <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Durability & SIM</dt>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/10">{{ $phone->body->ip_rating }}</span>
                             <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/10">{{ $phone->body->sim }}</span>
                        </div>
                    </div>
                     <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase mb-1">Colors</dt>
                         <dd class="text-sm font-medium text-black dark:text-white">{{ $phone->body->colors }}</dd>
                    </div>
                </div>
            </div>
            @endif

             <!-- Battery & Charging (New Section) -->
             @if($phone->battery)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5">
                <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Power</h3>
                    <p class="text-sm text-gray-500">Battery & Charging</p>
                </div>
                 <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl text-emerald-600">
                             <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                             <p class="text-3xl font-bold text-black dark:text-white">{{ $phone->battery->battery_type }}</p>
                             <p class="text-xs text-gray-500">Capacity</p>
                        </div>
                    </div>
                    <dl class="space-y-3">
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Wired</dt>
                            <dd class="text-sm font-bold text-black dark:text-white">{{ $phone->battery->charging_wired }}</dd>
                        </div>
                         <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Wireless</dt>
                            <dd class="text-sm font-bold text-black dark:text-white">{{ $phone->battery->charging_wireless }}</dd>
                        </div>
                         <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Reverse</dt>
                            <dd class="text-sm font-bold text-black dark:text-white">{{ $phone->battery->charging_reverse }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
            
             <!-- Connectivity Full Width -->
            @if($phone->connectivity)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 md:col-span-2 xl:col-span-1">
                 <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Connectivity</h3>
                    <p class="text-sm text-gray-500">Network & Sensors</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">WLAN</dt>
                        <dd class="text-xs font-medium text-black dark:text-white text-wrap leading-snug">{{ $phone->connectivity->wlan }}</dd>
                    </div>
                     <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                        <dt class="text-xs text-gray-500 uppercase">Bluetooth</dt>
                        <dd class="text-xs font-medium text-black dark:text-white">{{ $phone->connectivity->bluetooth }}</dd>
                    </div>
                     <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                        <dt class="text-xs text-gray-500 uppercase">GPS</dt>
                         <dd class="text-xs font-medium text-black dark:text-white truncate" title="{{ $phone->connectivity->positioning }}">{{ $phone->connectivity->positioning }}</dd>
                    </div>
                     <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">Features</dt>
                        <dd class="text-xs font-medium text-black dark:text-white">NFC: {{ $phone->connectivity->nfc }} • IR: {{ $phone->connectivity->infrared }} • Radio: {{ $phone->connectivity->radio }}</dd>
                        <dd class="text-xs font-medium text-black dark:text-white mt-1">USB: {{ $phone->connectivity->usb }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-xl col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">Sensors</dt>
                        <dd class="text-xs font-medium text-black dark:text-white text-wrap leading-snug mb-2">{{ $phone->connectivity->sensors }}</dd>
                         <dt class="text-xs text-gray-500 uppercase border-t border-gray-200 dark:border-gray-700 pt-2">Audio</dt>
                         <dd class="text-xs font-medium text-black dark:text-white">Speakers: {{ $phone->connectivity->loudspeaker }} • 3.5mm: {{ $phone->connectivity->jack_3_5mm }}</dd>
                    </div>
                </div>
            </div>
            @endif

            <!-- Development & Customization (New Detailed Section) -->
             @if($phone->platform)
            <div class="bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-8 shadow-sm ring-1 ring-gray-100 dark:ring-white/5 md:col-span-2 xl:col-span-1">
                <div class="mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                    <h3 class="text-xl font-bold text-black dark:text-white mb-1">Development & Customization</h3>
                    <p class="text-sm text-gray-500">Enthusiast Features</p>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <dt class="text-xs font-bold text-gray-400 uppercase">Bootloader</dt>
                             @if($phone->platform->bootloader_unlockable)
                            <span class="text-xs font-bold text-green-500">Unlockable</span>
                            @else
                            <span class="text-xs font-bold text-red-500">Locked</span>
                            @endif
                        </div>
                        <dd class="text-sm text-gray-600 dark:text-gray-300 leading-snug">
                             @if($phone->platform->bootloader_unlockable)
                            Unlocking the bootloader allows you to root the device, install custom ROMs, and fully control the software. Essential for longevity after official support ends.
                            @else
                            The bootloader cannot be officially unlocked, restricting custom ROMs and root access.
                            @endif
                        </dd>
                    </div>
                     <div>
                        <div class="flex items-center justify-between mb-1">
                            <dt class="text-xs font-bold text-gray-400 uppercase">Graphics Driver</dt>
                             @if($phone->platform->turnip_support)
                            <span class="text-xs font-bold text-purple-500">Turnip Support</span>
                            @else
                            <span class="text-xs font-bold text-gray-500">Standard</span>
                            @endif
                        </div>
                        <dd class="text-sm text-gray-600 dark:text-gray-300 leading-snug">
                             @if($phone->platform->turnip_support)
                            Supports open-source Turnip drivers (Adreno), offering significantly better performance and compatibility in emulators (Yuzu, Winlator, etc.) compared to stock drivers.
                            @else
                            Relying on proprietary drivers. May have compatibility or performance issues in advanced emulation scenarios compared to Adreno GPUs.
                            @endif
                        </dd>
                    </div>
                     <div>
                        <div class="flex items-center justify-between mb-1">
                            <dt class="text-xs font-bold text-gray-400 uppercase">AOSP Aesthetics</dt>
                            <span class="text-xs font-bold text-black dark:text-white">{{ $phone->platform->aosp_aesthetics_score }}/10</span>
                        </div>
                        <dd class="text-sm text-gray-600 dark:text-gray-300 leading-snug">
                             @if($phone->platform->aosp_aesthetics_score >= 8)
                            Minimal bloatware and a UI that closely adheres to stock Android/AOSP design guidelines. Clean, fast, and visually consistent.
                            @elseif($phone->platform->aosp_aesthetics_score >= 5)
                            Heavily skinned UI but maintains some AOSP elements. May contain pre-installed apps (bloatware) but functional.
                            @else
                            Far from stock Android. Heavily modified UI with significant bloatware and aggressive background app management.
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection
