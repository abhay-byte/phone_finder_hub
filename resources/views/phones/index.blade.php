@extends('layouts.app')

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
</style>

<div class="bg-gray-50 dark:bg-black min-h-screen">
    
    <!-- Hero Section -->
    <div class="relative bg-white dark:bg-[#121212] border-b border-gray-200 dark:border-white/5 overflow-hidden">
        <div class="absolute inset-0 bg-grid-slate-100 dark:bg-grid-slate-900/[0.04] bg-[bottom_1px_center] dark:bg-[bottom_1px_center]" style="mask-image: linear-gradient(to bottom, transparent, black);"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10 text-center">
            <h1 class="text-5xl md:text-7xl font-black tracking-tight text-slate-900 dark:text-white mb-6 animate-fadeInUp">
                Find Value, <span class="text-teal-600 dark:text-teal-500">Not Hype.</span>
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 font-medium leading-relaxed animate-fadeInUp delay-100">
                The only data-driven smartphone ranking based on real-world performance per rupee. No bias, just math.
            </p>
            
            <!-- Search & Filter Bar -->
            <div class="max-w-3xl mx-auto relative group animate-fadeInUp delay-200">
                <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-emerald-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative flex items-center bg-white dark:bg-[#1A1A1A] rounded-xl shadow-2xl ring-1 ring-gray-900/5 dark:ring-white/10 p-2">
                    <div class="pl-4 text-gray-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           placeholder="Search by name, brand, or chipset..." 
                           class="w-full bg-transparent border-0 focus:ring-0 text-lg text-slate-900 dark:text-white placeholder-slate-400 h-12"
                    >
                    <button class="bg-slate-900 dark:bg-white text-white dark:text-black px-6 py-3 rounded-lg font-bold hover:opacity-90 transition-opacity">
                        Search
                    </button>
                </div>
            </div>

            <!-- Quick Filters (Chips) -->
            <div class="flex flex-wrap justify-center gap-3 mt-8 animate-fadeInUp delay-300">
                <button class="px-5 py-2 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 text-sm font-semibold hover:bg-teal-100 dark:hover:bg-teal-900/40 transition-colors border border-teal-200 dark:border-teal-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    Top Value
                </button>
                <button class="px-5 py-2 rounded-full bg-white dark:bg-[#1A1A1A] text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-slate-200 dark:border-white/10 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    Gaming
                </button>
                <button class="px-5 py-2 rounded-full bg-white dark:bg-[#1A1A1A] text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-slate-200 dark:border-white/10 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Camera
                </button>
                <button class="px-5 py-2 rounded-full bg-white dark:bg-[#1A1A1A] text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-slate-200 dark:border-white/10 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Battery
                </button>
            </div>
        </div>
    </div>

    <!-- Phone Grid Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Latest Rankings</h2>
             <div class="flex items-center gap-2 text-sm text-slate-500">
                <span>Sort by:</span>
                <select class="bg-transparent border-none font-semibold text-slate-900 dark:text-white focus:ring-0 cursor-pointer">
                    <option>Value Score</option>
                    <option>Price: Low to High</option>
                    <option>Performance</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($phones as $phone)
            <a href="{{ route('phones.show', $phone) }}" class="group relative bg-white dark:bg-[#1A1A1A] rounded-[2rem] p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-white/5 flex flex-col h-full hover:-translate-y-1">
                
                <!-- Value Badge -->
                <div class="absolute top-4 right-4 z-10 flex flex-col items-end gap-2">
                    <div class="bg-black/5 dark:bg-white/10 backdrop-blur-md px-3 py-1 rounded-full border border-black/5 dark:border-white/5">
                        <span class="text-xs font-bold text-slate-900 dark:text-white">
                            {{ $phone->value_score }} <span class="text-slate-500 font-normal">pts/₹1k</span>
                        </span>
                    </div>
                </div>

                <!-- Image -->
                 <div class="relative w-full aspect-[4/5] mb-6 flex items-center justify-center p-4 bg-slate-50 dark:bg-black/20 rounded-[1.5rem] group-hover:bg-teal-50/30 dark:group-hover:bg-teal-900/10 transition-colors">
                    @if($phone->image_url)
                        <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal filter group-hover:scale-105 transition-transform duration-500">
                    @else
                         <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    @endif
                </div>

                <!-- Info -->
                <div class="mt-auto">
                    <p class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wide mb-1">{{ $phone->brand }}</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $phone->name }}</h3>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mb-4">₹{{ number_format($phone->price) }}</p>
                    
                     <!-- Mini Specs -->
                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-white/5 pt-4">
                        @if($phone->platform)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                            <span class="break-words line-clamp-2 text-left">{{ $phone->platform->chipset }}</span>
                        </div>
                        @endif
                        @if($phone->benchmarks)
                        <div class="flex items-center gap-1.5 justify-end">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                             <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($phone->benchmarks->antutu_score) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
