@extends('layouts.app')

@section('content')
    <div class="min-h-screen animate-fadeInUp">

        <!-- Modern Hero Section -->
        <div class="relative bg-gradient-to-b from-teal-50/80 via-white to-gray-100 dark:from-teal-950/40 dark:via-[#050505] dark:to-[#050505] pt-24 pb-28 md:pt-32 md:pb-40 border-b border-teal-100 dark:border-teal-900/30 z-10">

            <!-- Animated Background Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-500/10 rounded-full blur-[120px] animate-pulse"></div>
                <div class="absolute top-1/2 -right-24 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] animate-pulse delay-1000"></div>
                <div class="absolute bottom-0 left-1/3 w-64 h-64 bg-teal-400/5 rounded-full blur-[80px] animate-pulse delay-700"></div>
            </div>

            <div class="w-full px-4 sm:px-6 lg:px-8 relative text-center" style="z-index: 95;">
                <h1
                    class="text-6xl md:text-8xl font-black tracking-tighter text-slate-900 dark:text-white mb-8 leading-[0.9] md:leading-[0.85]">
                    <span class="inline-block animate-title-reveal">Find Value,</span>
                    <br class="hidden md:block">
                    <span class="inline-block animate-title-reveal delay-200">
                        <span class="inline-block bg-clip-text text-transparent bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-600 bg-[length:200%_auto] animate-gradient-x">
                            Not Hype.
                        </span>
                    </span>
                </h1>
                <p
                    class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-xl mx-auto mb-12 font-medium leading-relaxed animate-fadeInUp delay-500">
                    The only <span class="text-teal-600 dark:text-teal-400 font-bold italic">data-driven</span> smartphone ranking based on real-world performance per rupee. No bias, just math.
                </p>

                <!-- Search & Filter Bar -->
                <div class="max-w-3xl mx-auto relative group z-[100]" x-data="{
                    query: '',
                    results: [],
                    isLoading: false,
                    placeholder: '',
                    phrases: ['Search for OnePlus 15...', 'Search for Snapdragon 8 Gen 2...', 'Search for iPhone 16...', 'Search for Dimensity 9000...'],
                    phraseIndex: 0,
                    charIndex: 0,
                    isDeleting: false,
                    typeSpeed: 100,
                    init() {
                        this.typeLoop();
                        
                        // Keyboard shortcut listener
                        window.addEventListener('keydown', (e) => {
                            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                                e.preventDefault();
                                this.$refs.searchInput.focus();
                            }
                        });


                        this.$watch('query', (value) => {
                            if (value.length < 2) {
                                this.results = [];
                                return;
                            }
                            this.isLoading = true;
                            clearTimeout(this.debounce);
                            this.debounce = setTimeout(() => {
                                fetch(`/phones/search?query=${value}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        this.results = data;
                                        this.isLoading = false;
                                    });
                            }, 300);
                        });
                    },
                    formatPrice(price) {
                        if (!price || isNaN(price)) return 'N/A';
                        return '₹' + new Intl.NumberFormat('en-IN').format(price);
                    },
                    typeLoop() {
                        const currentPhrase = this.phrases[this.phraseIndex];
                
                        if (this.isDeleting) {
                            this.placeholder = currentPhrase.substring(0, this.charIndex - 1);
                            this.charIndex--;
                            this.typeSpeed = 50;
                        } else {
                            this.placeholder = currentPhrase.substring(0, this.charIndex + 1);
                            this.charIndex++;
                            this.typeSpeed = 100;
                        }
                
                        if (!this.isDeleting && this.charIndex === currentPhrase.length) {
                            this.isDeleting = true;
                            this.typeSpeed = 2000; // Pause at end
                        } else if (this.isDeleting && this.charIndex === 0) {
                            this.isDeleting = false;
                            this.phraseIndex = (this.phraseIndex + 1) % this.phrases.length;
                            this.typeSpeed = 500; // Pause before typing next
                        }
                
                        setTimeout(() => this.typeLoop(), this.typeSpeed);
                    }
                }">
                    <!-- Glow Effect -->
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-emerald-400 rounded-3xl blur-2xl opacity-20 group-hover:opacity-40 transition duration-700 animate-pulse">
                    </div>

                    <!-- Input Container -->
                    <div
                        class="relative flex items-center bg-white/80 dark:bg-black/40 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/20 dark:border-white/5 p-1.5 transition-all duration-500 group-hover:border-teal-500/30">
                        <div class="pl-6 text-teal-500/50">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" x-model="query" :placeholder="placeholder" x-ref="searchInput"

                            class="w-full bg-transparent border-0 focus:ring-0 text-xl font-medium text-slate-900 dark:text-white placeholder-slate-400/50 h-14 px-4 pr-16">
                        
                        <div class="absolute right-4 flex items-center gap-3">
                            <div x-show="isLoading" class="p-2">
                                <svg class="animate-spin h-5 w-5 text-teal-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-teal-500/10 text-teal-500 border border-teal-500/20 group-hover:scale-110 transition-transform duration-300">
                                <span class="text-xs font-black">⌘K</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="results.length > 0 && query.length >= 2"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2" @click.away="results = []"
                        class="absolute top-full left-0 right-0 mt-4 bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-[100] max-h-96 overflow-y-auto"
                        style="display: none;">
                        <template x-for="phone in results" :key="phone.id">
                            <a :href="`/phones/${phone.id}`"
                                class="block p-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-gray-100 dark:border-white/5 last:border-0 flex items-center gap-4 group">
                                <div
                                    class="h-16 w-16 bg-gray-100 dark:bg-white/5 rounded-xl p-2 flex items-center justify-center flex-shrink-0">
                                    <template x-if="phone.image">
                                        <img :src="phone.image" :alt="phone.name"
                                            class="h-full w-full object-contain mix-blend-multiply dark:mix-blend-normal transform group-hover:scale-110 transition-transform duration-300">
                                    </template>
                                    <template x-if="!phone.image">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-slate-900 dark:text-white text-lg font-display"
                                        x-text="phone.full_name"></div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 dark:bg-teal-900/30 text-teal-800 dark:text-teal-300">
                                            Value Score: <span x-text="phone.value_score || 'N/A'"
                                                class="ml-1 font-bold"></span>
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono"
                                            x-text="formatPrice(phone.price)"></span>
                                    </div>
                                </div>
                                <div class="text-slate-400 dark:text-slate-600 group-hover:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- Quick Filters (Revamped Chips) -->
                <div class="flex flex-wrap justify-center gap-4 mt-12 animate-fadeInUp delay-1000">
                    <!-- Top Value (active style) -->
                    <button
                        class="group relative px-6 py-2.5 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 border-2 border-teal-500 bg-teal-50 dark:bg-teal-900/30 shadow-md">
                        <span class="relative flex items-center gap-2 text-teal-700 dark:text-teal-400 text-sm font-black uppercase tracking-tighter">
                            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Top Value
                        </span>
                    </button>
                    
                    <!-- Gaming -->
                    <button
                        class="group relative px-6 py-2.5 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 border border-slate-300 dark:border-white/30 bg-white dark:bg-white/10 shadow-sm">
                        <span class="relative flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm font-bold uppercase tracking-tighter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Gaming
                        </span>
                    </button>

                    <!-- Camera -->
                    <button
                        class="group relative px-6 py-2.5 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 border border-slate-300 dark:border-white/30 bg-white dark:bg-white/10 shadow-sm">
                        <span class="relative flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm font-bold uppercase tracking-tighter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                            Camera
                        </span>
                    </button>

                    <!-- Battery -->
                    <button
                        class="group relative px-6 py-2.5 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 border border-slate-300 dark:border-white/30 bg-white dark:bg-white/10 shadow-sm">
                        <span class="relative flex items-center gap-2 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm font-bold uppercase tracking-tighter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Battery
                        </span>
                    </button>
                </div>

            </div>
        </div>

        <!-- AI Finder Advertisement Banner -->
        @include('phones.partials.ai_finder_banner')

        <!-- Phone Grid Section -->
        <div class="bg-slate-100 dark:bg-[#0a0a0a] border-t border-slate-200 dark:border-white/5">
        <div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 pb-12 lg:pb-16 pt-6 lg:pt-10 w-full">
            <div class="relative z-10 w-full" x-data="{
                searchOpen: false,
                sortOpen: false,
                currentSort: '{{ $sort }}',
                isLoading: false,
            
                init() {
                    window.addEventListener('popstate', (e) => {
                        const params = new URLSearchParams(window.location.search);
                        const sort = params.get('sort') || 'value_score';
                        if (this.currentSort !== sort) {
                            this.currentSort = sort;
                            this.fetchGrid(sort, false);
                        }
                    });
                },
            
                updateSort(value) {
                    this.currentSort = value;
                    this.sortOpen = false;
                    this.fetchGrid(value, true);
                },
            
                fetchGrid(newSort, pushState = true) {
                    const cacheKey = `phone_grid_${newSort}_v12`;
                    const cacheTTL = 5 * 60 * 1000;
            
                    this.isLoading = true;
            
                    if (pushState) {
                        const url = new URL(window.location);
                        url.searchParams.set('sort', newSort);
                        window.history.pushState({ sort: newSort }, '', url);
                    }
            
                    const cached = localStorage.getItem(cacheKey);
                    if (cached) {
                        const data = JSON.parse(cached);
                        const now = new Date().getTime();
                        if (now - data.timestamp < cacheTTL) {
                            setTimeout(() => {
                                this.$refs.gridContainer.innerHTML = data.html;
                                this.isLoading = false;
                            }, 300);
                            return;
                        }
                    }
            
                    fetch(`{{ route('phones.grid') }}?sort=${newSort}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            if (!html.includes('<!DOCTYPE html>')) {
                                setTimeout(() => {
                                    this.$refs.gridContainer.innerHTML = html;
                                    this.isLoading = false;
                                    localStorage.setItem(cacheKey, JSON.stringify({
                                        timestamp: new Date().getTime(),
                                        html: html
                                    }));
                                }, 300);
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.location.reload();
                        });
                }
            }">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 px-2 xl:px-0 mt-4 lg:mt-0">
                    <h2
                        class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        Latest Rankings
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-white/10 text-gray-800 dark:text-gray-200">
                            Updated Live
                        </span>
                    </h2>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <span
                            class="text-sm font-medium text-slate-500 dark:text-slate-400 hidden sm:block uppercase tracking-wider">Sort
                            by:</span>
                        <div class="relative w-full sm:w-auto shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="w-full flex items-center gap-2 bg-white dark:bg-[#161616] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-white/5 hover:border-teal-300 dark:hover:border-teal-500/50 transition-all focus:outline-none focus:ring-4 focus:ring-teal-500/20 sm:min-w-[210px] justify-between group relative overflow-hidden">
                                <span class="relative z-10 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    <span x-show="currentSort === 'expert_score'">Expert Score</span>
                                    <span x-show="currentSort === 'value_score'">Value Score</span>
                                    <span x-show="currentSort === 'price_asc'">Price: Low to High</span>
                                    <span x-show="currentSort === 'overall_score'">Performance</span>
                                    <span x-show="currentSort === 'ueps_score'">UEPS Score</span>
                                </span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-500 transition-transform duration-300 relative z-10"
                                    :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]"
                                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]"
                                class="absolute right-0 sm:right-0 left-0 sm:left-auto mt-2 w-full sm:w-64 bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 py-2 z-[100] origin-top flex flex-col focus:outline-none ring-1 ring-black/5 dark:ring-white/5"
                                style="display: none;">

                                <div class="px-4 py-2 border-b border-gray-100 dark:border-white/5 mb-1">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Select
                                        Metric</span>
                                </div>
                                @foreach ([
            'expert_score' => 'Expert Score (Recommended)',
            'value_score' => 'Value Score (Best Bang)',
            'price_asc' => 'Price: Low to High',
            'overall_score' => 'Raw Performance (Benchmarks)',
            'ueps_score' => 'UEPS Score (Endurance)',
        ] as $key => $label)
                                    <button @click="updateSort('{{ $key }}'); open = false"
                                        class="w-full flex items-center justify-between px-4 py-3 text-sm text-left text-slate-600 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-900/10 hover:text-teal-700 dark:hover:text-teal-400 transition-colors group"
                                        :class="{ 'bg-teal-50/50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 font-semibold': currentSort === '{{ $key }}' }">
                                        <span class="truncate pr-4">{{ $label }}</span>
                                        <template x-if="currentSort === '{{ $key }}'">
                                            <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </template>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-5 lg:gap-6 relative z-10"
                    x-ref="gridContainer">
                    <!-- Skeleton Loading Grid -->
                    <div x-show="isLoading"
                        class="absolute inset-0 bg-gray-50/80 dark:bg-[#0a0a0a]/80 backdrop-blur-md z-40 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-5 lg:gap-6"
                        style="display: none;">

                        @for ($i = 0; $i < 10; $i++)
                            <div
                                class="bg-white dark:bg-[#161616] rounded-[2rem] p-5 shadow-sm border border-gray-100 dark:border-white/5 h-full flex flex-col">
                                <div class="relative mb-6">
                                    <div
                                        class="h-56 w-full bg-gray-100 dark:bg-white/5 rounded-2xl skeleton skeleton-shimmer">
                                    </div>
                                    <div
                                        class="absolute -bottom-3 right-4 h-6 w-20 rounded-full bg-gray-200 dark:bg-white/10 skeleton skeleton-shimmer">
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col">
                                    <div class="mb-4 space-y-2">
                                        <div
                                            class="h-4 w-16 bg-gray-200 dark:bg-white/10 rounded skeleton skeleton-shimmer">
                                        </div>
                                        <div
                                            class="h-6 w-3/4 bg-gray-200 dark:bg-white/10 rounded skeleton skeleton-shimmer">
                                        </div>
                                    </div>
                                    <div class="space-y-3 mb-6 flex-1">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div
                                                class="h-9 bg-gray-100 dark:bg-white/5 rounded-lg skeleton skeleton-shimmer">
                                            </div>
                                            <div
                                                class="h-9 bg-gray-100 dark:bg-white/5 rounded-lg skeleton skeleton-shimmer">
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-white/5 mt-auto">
                                        <div
                                            class="h-5 w-24 bg-gray-200 dark:bg-white/10 rounded skeleton skeleton-shimmer">
                                        </div>
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 dark:bg-white/10 skeleton skeleton-shimmer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    {!! $gridHtml !!}
                </div>
            </div>
        </div>
        </div>{{-- end bg-slate wrapper --}}
    </div>
@endsection
