@extends('layouts.app')

@section('title', 'Compare Phones')

@section('content')
<div class="bg-white dark:bg-black min-h-screen pb-20 pt-24 selection:bg-teal-500 selection:text-white font-sans" 
     x-data="comparisonPage()">

    <!-- Main Container for Header & Search -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-up">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-2">
                    Compare <span class="text-teal-600 dark:text-teal-500">Devices</span>
                </h1>
                <p class="text-slate-600 dark:text-slate-400 font-medium">
                    Side-by-side specs, benchmarks, and value analysis.
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3">
                 <button @click="clearAll()" 
                        x-show="phones.length > 0"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-900/30">
                    Clear All
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="phones.length === 0" x-cloak
             class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-gray-200 dark:border-white/10 rounded-[2.5rem] bg-gray-50/50 dark:bg-white/5 backdrop-blur-sm w-full animate-fade-in-up">
            <div class="w-24 h-24 bg-teal-50 dark:bg-teal-900/20 rounded-full flex items-center justify-center text-teal-600 mb-8">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Start Comparing</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto mb-10 text-lg leading-relaxed">
                Add huge flagship phones or budget beasts to see who truly wins on value and performance.
            </p>
            <button @click="openSearch(0)" class="inline-flex items-center justify-center px-10 py-4 bg-teal-600 hover:bg-teal-700 text-white text-lg font-bold rounded-full shadow-xl shadow-teal-500/20 transition-all transform hover:scale-105 active:scale-95">
                <span>Add First Device</span>
            </button>
        </div>
    </div>

    <!-- Comparison Table Container (Full Width / Flexible Grid) -->
    <div x-show="phones.length > 0" class="w-full px-4 sm:px-6 lg:px-8 xl:px-12" x-transition.opacity>
        <div class="overflow-x-auto pb-4 hide-scrollbar">
            <div class="min-w-max md:min-w-0 w-full">
                
                <!-- Dynamic Grid Config -->
                <div class="grid gap-0 relative w-full"
                     style="--label-width: 140px; --phone-width: 260px; @media (min-width: 768px) { --label-width: 200px; --phone-width: 280px; }"
                     :style="`grid-template-columns: var(--label-width) repeat(${phones.length}, minmax(var(--phone-width), 1fr)) ${phones.length < 4 ? 'minmax(var(--phone-width), 1fr)' : ''}`">

                    <!-- STICKY HEADER ROW -->
                    <div class="contents group/header">
                        <!-- Top-Left Corner (Empty/Sticky) -->
                        <div class="sticky top-0 left-0 z-50 bg-white/95 dark:bg-black/95 backdrop-blur-xl border-b border-r border-gray-200 dark:border-white/10 p-6 flex items-end pb-8">
                             <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Specifications</span>
                        </div>

                        <!-- Phone Headers -->
                        <template x-for="(phone, index) in phones" :key="phone.id">
                            <div class="sticky top-0 z-40 bg-white/95 dark:bg-black/95 backdrop-blur-xl border-b border-r border-gray-200 dark:border-white/10 p-8 flex flex-col items-center text-center relative transition-colors hover:bg-gray-50 dark:hover:bg-[#121212]">
                                <!-- Remove Button -->
                                <button @click="removePhone(phone.id)" 
                                        class="absolute top-4 right-4 p-2 text-gray-300 hover:text-red-500 transition-colors bg-white dark:bg-white/5 rounded-full shadow-sm hover:shadow-md border border-gray-100 dark:border-white/5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Image -->
                                <div class="h-48 w-full mb-6 flex items-center justify-center p-4">
                                    <img :src="phone.image_url" :alt="phone.name" 
                                         class="max-h-full max-w-full object-contain filter drop-shadow-2xl transition-transform duration-500 hover:scale-110 will-change-transform">
                                </div>
                                
                                <!-- Name & Price -->
                                <div class="mb-6">
                                    <a :href="`{{ url('/phones') }}/${phone.id}`" class="hover:underline decoration-teal-500 decoration-2 underline-offset-4">
                                        <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white leading-tight mb-2" x-text="phone.name"></h3>
                                    </a>
                                    <p class="text-xl font-bold font-mono text-teal-600 dark:text-teal-400" x-text="formatPrice(phone.price)"></p>
                                </div>

                                <!-- Top Metrics Mini-Bars -->
                                <div class="w-full space-y-4">
                                    <!-- UEPS -->
                                    <div class="w-full">
                                        <div class="flex justify-between items-end text-xs mb-1.5">
                                            <span class="text-gray-400 font-bold uppercase tracking-wider">UEPS 4.5</span>
                                            <div class="flex items-center gap-1.5">
                                                 <template x-if="isWinner(phone, 'ueps_score')"><span class="text-[10px] bg-teal-100 dark:bg-teal-900/50 text-teal-800 dark:text-teal-200 px-1.5 py-0.5 rounded font-bold flex items-center gap-1 border border-teal-200 dark:border-teal-800">👑 Best</span></template>
                                                 <span class="text-lg font-black text-gray-900 dark:text-white" x-text="formatScore(phone.ueps_score)"></span>
                                            </div>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-teal-500 shadow-[0_0_10px_rgba(20,184,166,0.3)]"
                                                 :style="`width: ${getBarWidth(phone, 'ueps_score')}%`"></div>
                                        </div>
                                    </div>
                                    <!-- FPI -->
                                    <div class="w-full">
                                        <div class="flex justify-between items-end text-xs mb-1.5">
                                            <span class="text-gray-400 font-bold uppercase tracking-wider">Perf. Index</span>
                                            <div class="flex items-center gap-1.5">
                                                 <template x-if="isWinner(phone, 'overall_score')"><span class="text-[10px] bg-teal-100 dark:bg-teal-900/50 text-teal-800 dark:text-teal-200 px-1.5 py-0.5 rounded font-bold flex items-center gap-1 border border-teal-200 dark:border-teal-800">👑 Best</span></template>
                                                 <span class="text-lg font-black text-gray-900 dark:text-white" x-text="formatScore(phone.overall_score)"></span>
                                            </div>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-teal-500/80 shadow-[0_0_10px_rgba(20,184,166,0.3)]"
                                                 :style="`width: ${getBarWidth(phone, 'overall_score')}%`"></div>
                                        </div>
                                    </div>
                                    <!-- Value -->
                                    <div class="w-full">
                                        <div class="flex justify-between items-end text-xs mb-1.5">
                                            <span class="text-gray-400 font-bold uppercase tracking-wider">Value Score</span>
                                             <div class="flex items-center gap-1.5">
                                                 <template x-if="isWinner(phone, 'value_score')"><span class="text-[10px] bg-teal-100 dark:bg-teal-900/50 text-teal-800 dark:text-teal-200 px-1.5 py-0.5 rounded font-bold flex items-center gap-1 border border-teal-200 dark:border-teal-800">👑 Best</span></template>
                                                 <span class="text-lg font-black text-gray-900 dark:text-white" x-text="formatScore(phone.value_score)"></span>
                                            </div>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-teal-500/60 shadow-[0_0_10px_rgba(20,184,166,0.3)]"
                                                 :style="`width: ${getBarWidth(phone, 'value_score')}%`"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Add Button Header Slot -->
                         <div x-show="phones.length < 4" 
                              class="sticky top-0 z-40 bg-white/95 dark:bg-black/95 backdrop-blur-xl border-b border-gray-200 dark:border-white/10 p-8 flex flex-col items-center justify-center">
                            <button @click="openSearch(phones.length)" 
                                    class="w-full h-full min-h-[400px] border-2 border-dashed border-gray-300 dark:border-white/10 rounded-[2rem] flex flex-col items-center justify-center gap-6 hover:border-teal-500 dark:hover:border-teal-500 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 transition-all group active:scale-95 duration-200">
                                <div class="w-20 h-20 rounded-full bg-white dark:bg-white/5 shadow-lg border border-gray-100 dark:border-white/10 flex items-center justify-center text-gray-300 group-hover:text-teal-500 transition-colors">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-400 dark:text-gray-500 group-hover:text-teal-500 uppercase tracking-wider text-sm">Add Device</span>
                            </button>
                        </div>
                    </div>

                    <!-- SPECS ROWS -->
                    <template x-for="section in specs" :key="section.title">
                        <div class="contents">
                            <!-- Section Title Row -->
                            <div class="col-span-full bg-gray-100 dark:bg-[#121212] border-b border-gray-200 dark:border-white/10 py-3 px-6 mt-0 sticky left-0">
                                <h4 class="text-xs font-black uppercase tracking-widest text-teal-600 dark:text-teal-400" x-text="section.title"></h4>
                            </div>

                            <!-- Data Rows -->
                            <template x-for="row in section.rows" :key="row.key">
                                <div class="contents hover:bg-white dark:hover:bg-[#121212] transition-colors group">
                                    
                                    <!-- Label Column -->
                                    <div class="sticky left-0 bg-gray-50 dark:bg-black group-hover:bg-white dark:group-hover:bg-[#121212] border-r border-b border-gray-200 dark:border-white/5 p-6 flex items-center z-30 transition-colors">
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-relaxed" x-text="row.label"></span>
                                    </div>

                                    <!-- Phone Values -->
                                    <template x-for="(phone, index) in phones" :key="phone.id">
                                        <div class="border-b border-r border-gray-200 dark:border-white/5 p-6 flex items-center justify-center text-center relative group-hover:bg-gray-50/30 dark:group-hover:bg-white/[0.02] transition-colors">
                                            
                                            <!-- Standard Text -->
                                            <template x-if="section.title !== 'Raw Benchmarks'">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-relaxed" x-html="getSpecValue(phone, row.key)"></span>
                                            </template>

                                            <!-- Benchmarks Bars -->
                                            <template x-if="section.title === 'Raw Benchmarks'">
                                                <div class="w-full">
                                                    <div class="flex justify-between items-end mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <template x-if="isWinner(phone, row.key)">
                                                                <span class="text-[10px] bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider border border-teal-200 dark:border-teal-800">👑 Best</span>
                                                            </template>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <template x-if="!isWinner(phone, row.key) && phones.length > 1">
                                                                <span class="text-red-500 text-[10px] font-bold" x-text="`-${getPercentageDiff(phone, row.key)}%`"></span>
                                                            </template>
                                                            <span class="text-sm font-black font-mono text-gray-900 dark:text-white"
                                                                  :class="{ 'text-teal-600 dark:text-teal-400': isWinner(phone, row.key) }"
                                                                  x-text="formatScore(getSpecValue(phone, row.key))"></span>
                                                        </div>
                                                    </div>
                                                    <div class="h-3 w-full bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                                                        <div class="h-full rounded-full shadow-lg transition-all duration-700 ease-out"
                                                             :class="isWinner(phone, row.key) ? 'bg-teal-500 shadow-[0_0_10px_rgba(20,184,166,0.3)]' : 'bg-gray-300 dark:bg-gray-700'"
                                                             :style="`width: ${getBarWidth(phone, row.key)}%`"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Empty Cell for Add Button Column -->
                                     <div x-show="phones.length < 4" class="border-b border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-black/50"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Modal (Spotlight Style) -->
    <div x-show="isSearchOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none; z-index: 9999;" x-cloak
         @keydown.escape.window="isSearchOpen = false">
        
        <div x-show="isSearchOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="isSearchOpen = false"
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-md"
             style="z-index: 9998;"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isSearchOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative mx-auto w-full max-w-2xl bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-[9999]">
                
                <div class="relative border-b border-gray-100 dark:border-white/5">
                    <svg class="pointer-events-none absolute top-4 left-4 h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch()"
                           class="h-14 w-full border-0 bg-transparent pl-14 pr-4 text-gray-900 dark:text-white placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                           placeholder="Search for a phone (e.g. OnePlus 12)..."
                           autofocus>
                </div>
                
                <ul class="max-h-[60vh] overflow-y-auto py-2">
                    <template x-if="isLoading">
                        <li class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Searching...</li>
                    </template>

                    <template x-if="!isLoading && searchResults.length === 0 && searchQuery.length >= 2">
                        <li class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No phones found.</li>
                    </template>

                    <template x-for="result in searchResults" :key="result.id">
                        <li @click="selectPhone(result)"
                            class="cursor-pointer px-4 py-3 hover:bg-teal-50 dark:hover:bg-white/5 flex items-center gap-4 transition-colors">
                            <img :src="result.image" class="w-10 h-10 object-contain" alt="">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white" x-text="result.name"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="result.brand"></div>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('comparisonPage', () => ({
                phones: @json($phones), // Initial payload from controller
                isSearchOpen: false,
                searchQuery: '',
                searchResults: [],
                isLoading: false,
                currentSlot: null,
                isScrolled: false,

                specs: [
                    {
                        title: 'Top Metrics',
                        rows: [] // Handled in header now
                    },
                    {
                        title: 'Raw Benchmarks',
                        rows: [
                            { key: 'benchmarks.antutu_score', label: 'AnTuTu v11' },
                            { key: 'benchmarks.geekbench_single', label: 'Geekbench 6 (Single)' },
                            { key: 'benchmarks.geekbench_multi', label: 'Geekbench 6 (Multi)' },
                            { key: 'benchmarks.dmark_wild_life_extreme', label: '3DMark Wild Life Extreme' }
                        ]
                    },
                    {
                        title: 'Network',
                        rows: [
                            { key: 'connectivity.network_bands', label: 'Technology' },
                            { key: 'connectivity.sar_value', label: 'SAR Value' },
                        ]
                    },
                    {
                        title: 'Body',
                        rows: [
                            { key: 'body.dimensions', label: 'Dimensions' },
                            { key: 'body.weight', label: 'Weight' },
                            { key: 'body.build_material', label: 'Build' },
                            { key: 'body.sim', label: 'SIM' },
                            { key: 'body.ip_rating', label: 'IP Rating' }
                        ]
                    },
                    {
                        title: 'Display',
                        rows: [
                            { key: 'body.display_type', label: 'Type' },
                            { key: 'body.display_size', label: 'Size' },
                            { key: 'body.display_resolution', label: 'Resolution' },
                            { key: 'body.pixel_density', label: 'Density' },
                            { key: 'body.screen_to_body_ratio', label: 'Screen-to-Body' },
                            { key: 'body.display_brightness', label: 'Brightness' },
                            { key: 'body.pwm_dimming', label: 'PWM Dimming' },
                            { key: 'body.touch_sampling_rate', label: 'Touch Sampling' },
                            { key: 'body.display_protection', label: 'Protection' },
                            { key: 'body.screen_glass', label: 'Glass' },
                        ]
                    },
                    {
                        title: 'Platform',
                        rows: [
                            { key: 'platform.os', label: 'OS' },
                            { key: 'platform.chipset', label: 'Chipset' },
                            { key: 'platform.cpu', label: 'CPU' },
                            { key: 'platform.gpu', label: 'GPU' }
                        ]
                    },
                    {
                        title: 'Memory',
                        rows: [
                            { key: 'platform.memory_card_slot', label: 'Card Slot' },
                            { key: 'platform.internal_storage', label: 'Internal' },
                            { key: 'platform.ram', label: 'RAM' },
                            { key: 'platform.storage_type', label: 'Storage Type' }
                        ]
                    },
                    {
                        title: 'Main Camera',
                        rows: [
                            { key: 'camera.main_camera_specs', label: 'Modules' },
                            { key: 'camera.main_camera_sensors', label: 'Sensors' },
                            { key: 'camera.main_camera_apertures', label: 'Apertures' },
                            { key: 'camera.main_camera_focal_lengths', label: 'Focal Lengths' },
                            { key: 'camera.main_camera_ois', label: 'OIS' },
                            { key: 'camera.main_camera_features', label: 'Features' },
                            { key: 'camera.main_video_capabilities', label: 'Video' }
                        ]
                    },
                    {
                        title: 'Selfie Camera',
                        rows: [
                            { key: 'camera.selfie_camera_specs', label: 'Modules' },
                            { key: 'camera.selfie_camera_features', label: 'Features' },
                            { key: 'camera.selfie_video_capabilities', label: 'Video' }
                        ]
                    },
                    {
                        title: 'Sound',
                        rows: [
                            { key: 'connectivity.loudspeaker', label: 'Loudspeaker' },
                            { key: 'connectivity.audio_quality', label: 'Audio Quality' },
                            { key: 'connectivity.has_3_5mm_jack', label: '3.5mm Jack' }
                        ]
                    },
                    {
                        title: 'Comms',
                        rows: [
                            { key: 'connectivity.wlan', label: 'WLAN' },
                            { key: 'connectivity.bluetooth', label: 'Bluetooth' },
                            { key: 'connectivity.positioning', label: 'Positioning' },
                            { key: 'connectivity.nfc', label: 'NFC' },
                            { key: 'connectivity.infrared', label: 'Infrared' },
                            { key: 'connectivity.radio', label: 'Radio' },
                            { key: 'connectivity.usb', label: 'USB' }
                        ]
                    },
                    {
                        title: 'Features',
                        rows: [
                            { key: 'connectivity.sensors', label: 'Sensors' }
                        ]
                    },
                    {
                        title: 'Battery',
                        rows: [
                            { key: 'battery.battery_type', label: 'Type' },
                            { key: 'battery.charging_specs_detailed', label: 'Charging' },
                            { key: 'battery.charging_reverse', label: 'Reverse' },
                        ]
                    },
                    {
                        title: 'Misc',
                        rows: [
                            { key: 'body.colors', label: 'Colors' },
                            { key: 'benchmarks.repairability_score', label: 'Repairability' },
                            { key: 'benchmarks.energy_label', label: 'Energy Label' },
                            { key: 'price', label: 'Price' }
                        ]
                    }
                ],

                init() {
                    // Sync initial state
                    this.syncState();
                    window.addEventListener('keydown', (e) => {
                        if (e.key === '/' && !this.isSearchOpen) {
                            e.preventDefault();
                            this.openSearch(this.phones.length);
                        }
                    });
                },

                getMax(key) {
                    if (this.phones.length === 0) return 0;
                    const getValue = (phone) => {
                        const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
                        return parseFloat(val) || 0;
                    };
                    return Math.max(...this.phones.map(getValue));
                },

                getBarWidth(phone, key) {
                    const max = this.getMax(key);
                    if (max === 0) return 0;
                    const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
                    const score = parseFloat(val) || 0;
                    return (score / max) * 100;
                },

                getPercentageDiff(phone, key) {
                    const max = this.getMax(key);
                    if (max === 0) return 0;
                    const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
                    const score = parseFloat(val) || 0;
                    if (score === max) return 0;
                    const diff = (1 - (score / max)) * 100;
                    return Math.round(diff);
                },

                isWinner(phone, key) {
                     if (this.phones.length < 2) return false;
                     const max = this.getMax(key);
                     if (max === 0) return false;
                     const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
                     const score = parseFloat(val) || 0;
                     return score === max;
                },

                formatScore(val) {
                    if (!val) return '-';
                    return new Intl.NumberFormat('en-IN').format(val);
                },

                getSpecValue(phone, key) {
                    if (key === 'price') {
                         return this.formatPrice(phone.price);
                    }
                    let val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
                    if (!val) return '<span class="text-gray-400">-</span>';
                    return val.toString().replace(/\n/g, '<br>');
                },

                formatPrice(price) {
                    if (!price) return 'N/A';
                    return new Intl.NumberFormat('en-IN', {
                        style: 'currency',
                        currency: 'INR',
                        maximumFractionDigits: 0
                    }).format(price);
                },

                openSearch(index) {
                    this.isSearchOpen = true;
                    this.currentSlot = index;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.$nextTick(() => document.querySelector('input[x-model="searchQuery"]').focus());
                },

                async performSearch() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const response = await fetch(`{{ route('phones.search') }}?query=${encodeURIComponent(this.searchQuery)}`);
                        let data = await response.json();
                        const currentIds = this.phones.map(p => p.id);
                        this.searchResults = data.filter(p => !currentIds.includes(p.id));
                    } catch (e) {
                        console.error('Search failed', e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                selectPhone(result) {
                    if (!result || !result.id) return;
                    window.location.assign(`{{ route('phones.compare') }}?ids=${this.getNewIds(result.id)}`);
                },

                removePhone(id) {
                     const newIds = this.phones.filter(p => p.id !== id).map(p => p.id).join(',');
                     window.location.assign(`{{ route('phones.compare') }}?ids=${newIds}`);
                },
                
                clearAll() {
                    window.location.assign(`{{ route('phones.compare') }}`);
                },

                getNewIds(newId) {
                    const currentIds = this.phones.map(p => p.id);
                    return [...currentIds, newId].join(',');
                },

                syncState() {
                     // Local storage sync if needed in future
                }
            }));
        });
    </script>
</div>
@endsection
