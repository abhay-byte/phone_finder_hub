@extends('layouts.app')

@section('content')
    <div
        class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-teal-500 selection:text-white animate-fadeInUp">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Smartphone Rankings
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                @if($tab == 'overall')
                <!-- Sidebar (Only on Expert Tab) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Filter Card -->
                    <div class="bg-white dark:bg-[#121212] rounded-2xl shadow-sm border border-slate-200 dark:border-white/5 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Filters</h3>
                            <button id="reset-filters" class="text-xs text-slate-500 hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-400 font-medium transition-colors">
                                Reset
                            </button>
                        </div>
                        
                        <!-- ToolCool CDN -->
                        <script src="https://cdn.jsdelivr.net/npm/toolcool-range-slider/dist/toolcool-range-slider.min.js"></script>

                        <!-- Price Range -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Price Range</label>
                            <tc-range-slider
                                id="price-slider"
                                min="0"
                                max="{{ $maxDatabasePrice ?? 200000 }}"
                                step="1000"
                                value1="{{ $minPrice ?? 0 }}"
                                value2="{{ $maxPrice ?? $maxDatabasePrice ?? 200000 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="price-min-display">₹{{ number_format($minPrice ?? 0) }}</span>
                                <span id="price-max-display">₹{{ number_format($maxPrice ?? $maxDatabasePrice ?? 200000) }}</span>
                            </div>
                            <input type="hidden" id="min_price" name="min_price" value="{{ $minPrice ?? 0 }}">
                            <input type="hidden" id="max_price" name="max_price" value="{{ $maxPrice ?? $maxDatabasePrice ?? 200000 }}">
                        </div>

                        <!-- RAM Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">RAM (GB)</label>
                            <tc-range-slider
                                id="ram-slider"
                                min="2"
                                max="24"
                                step="1"
                                value1="{{ $minRam ?? 4 }}"
                                value2="{{ $maxRam ?? 24 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="ram-min-display">{{ $minRam ?? 4 }} GB</span>
                                <span id="ram-max-display">{{ $maxRam ?? 24 }} GB</span>
                            </div>
                            <input type="hidden" id="min_ram" name="min_ram" value="{{ $minRam ?? 4 }}">
                            <input type="hidden" id="max_ram" name="max_ram" value="{{ $maxRam ?? 24 }}">
                        </div>

                        <!-- Storage Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Storage</label>
                            <tc-range-slider
                                id="storage-slider"
                                min="32"
                                max="1024"
                                step="32"
                                value1="{{ $minStorage ?? 64 }}"
                                value2="{{ $maxStorage ?? 1024 }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="storage-min-display">{{ $minStorage < 1000 ? $minStorage . ' GB' : ($minStorage/1024) . ' TB' }}</span>
                                <span id="storage-max-display">{{ $maxStorage < 1000 ? $maxStorage . ' GB' : ($maxStorage/1024) . ' TB' }}</span>
                            </div>
                            <input type="hidden" id="min_storage" name="min_storage" value="{{ $minStorage ?? 64 }}">
                            <input type="hidden" id="max_storage" name="max_storage" value="{{ $maxStorage ?? 1024 }}">
                        </div>

                        <!-- Brand Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Brands</label>
                            <div class="max-h-40 overflow-y-auto space-y-2 p-2 border border-slate-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-black/20">
                                @foreach($filterOptions['brands'] as $brand)
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/5 rounded px-1 py-0.5 transition-colors">
                                        <input type="checkbox" name="brands[]" value="{{ $brand }}" 
                                            class="rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:bg-white/5 dark:border-white/10"
                                            {{ in_array($brand, request('brands', [])) ? 'checked' : '' }}>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">{{ $brand }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- IP Rating Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">IP Rating</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($filterOptions['ip_ratings'] as $ip)
                                    <label class="cursor-pointer group relative">
                                        <input type="checkbox" name="ip_ratings[]" value="{{ $ip }}" 
                                            class="peer hidden" style="display:none"
                                            {{ in_array($ip, request('ip_ratings', [])) ? 'checked' : '' }}>
                                        <span class="inline-block px-3 py-1.5 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 text-xs font-bold text-slate-600 dark:text-slate-400 peer-checked:bg-teal-50 peer-checked:text-teal-700 peer-checked:border-teal-200 dark:peer-checked:bg-teal-900/30 dark:peer-checked:text-teal-300 dark:peer-checked:border-teal-800 transition-all hover:bg-slate-50 dark:hover:bg-white/10">
                                            {{ \Illuminate\Support\Str::before($ip, ' ') }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                         <!-- AnTuTu Filter -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">AnTuTu Score</label>
                            <tc-range-slider
                                id="antutu-slider"
                                min="0"
                                max="{{ $filterOptions['max_antutu'] }}"
                                step="10000"
                                value1="{{ request('min_antutu', 0) }}"
                                value2="{{ request('max_antutu', $filterOptions['max_antutu']) }}"
                                round="0"
                                slider-width="100%"
                                slider-height="12px"
                                slider-radius="6px"
                                pointer-width="4px"
                                pointer-height="24px"
                                pointer-radius="2px"
                                slider-bg="#e2e8f0"
                                slider-bg-hover="#e2e8f0"
                                slider-bg-fill="#0d9488"
                                pointer-bg="#ffffff"
                                pointer-bg-hover="#ffffff"
                                pointer-bg-focus="#ffffff"
                                pointer-shadow="0 1px 3px rgba(0,0,0,0.3)"
                                pointer-shadow-hover="0 2px 5px rgba(0,0,0,0.4)"
                                pointer-shadow-focus="0 2px 5px rgba(0,0,0,0.4)"
                            ></tc-range-slider>
                            <div class="flex items-center justify-between text-sm font-mono text-slate-600 dark:text-slate-400 mt-2">
                                <span id="antutu-min-display">{{ number_format(request('min_antutu', 0)) }}</span>
                                <span id="antutu-max-display">{{ number_format(request('max_antutu', $filterOptions['max_antutu'])) }}</span>
                            </div>
                            <input type="hidden" id="min_antutu" name="min_antutu" value="{{ request('min_antutu', 0) }}">
                            <input type="hidden" id="max_antutu" name="max_antutu" value="{{ request('max_antutu', $filterOptions['max_antutu']) }}">
                        </div>

                        <!-- Enthusiast Filters -->
                        <div class="mb-8 space-y-4">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Unlock Bootloader</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="bootloader" name="bootloader" value="1" {{ $bootloader ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                </div>
                            </label>
                            
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Turnip Driver Support</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="turnip" name="turnip" value="1" {{ $turnip ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between cursor-pointer group">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Show Unverified</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-500 font-medium">Include phones with missing data</span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="show_unverified" name="show_unverified" value="1" {{ $showUnverified ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                </div>
                            </label>
                        </div>

                        <button id="apply-filters"
                            class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-teal-500/20 active:scale-95">
                            Apply Filters
                        </button>
                    </div>
                </div>
                @endif

                <!-- Main Content -->
                <div class="{{ $tab == 'overall' ? 'lg:col-span-3' : 'lg:col-span-4' }} space-y-6">
                    <div class="flex flex-col md:flex-row justify-end items-start md:items-center gap-4">
                        <!-- Tabs -->
                        <div id="tabs-container" class="bg-gray-200 dark:bg-white/10 p-1.5 rounded-xl inline-flex font-bold text-sm overflow-x-auto max-w-full">
                    <a href="{{ route('phones.rankings', ['tab' => 'overall']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'overall' ? 'bg-white dark:bg-black shadow-sm text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Expert Score
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'ueps']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'ueps' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        UEPS 45
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'performance']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'performance' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Performance
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'gaming']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'gaming' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Gaming (GPX)
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'cms']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'cms' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Camera (CMS)
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'endurance']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'endurance' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Endurance
                    </a>
                    <a href="{{ route('phones.rankings', ['tab' => 'value']) }}"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab == 'value' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Value
                    </a>
                </div>
            </div>

            <!-- Data Table -->
            {!! $tableHtml !!}
            </div> <!-- End Main Content -->
        </div> <!-- End Grid -->

        </div>
    </div>



    <style>
        /* Force ToolCool Slider Handle to White */
        tc-range-slider {
            --pointer-bg: #ffffff !important;
            --pointer-bg-hover: #ffffff !important;
            --pointer-bg-focus: #ffffff !important;
            --pointer-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
            --pointer-shadow-hover: 0 2px 5px rgba(0,0,0,0.4) !important;
            --pointer-shadow-focus: 0 2px 5px rgba(0,0,0,0.4) !important;
        }
    </style>

    <script>
        function initFilters() {
            // Select buttons
            const applyBtn = document.getElementById('apply-filters');
            const resetBtn = document.getElementById('reset-filters');
            
            // --- ToolCool Slider Event Listeners ---
            const attachSliderListeners = (sliderId, minInputId, maxInputId, minDisplayId, maxDisplayId, formatFn) => {
                const slider = document.getElementById(sliderId);
                const minInput = document.getElementById(minInputId);
                const maxInput = document.getElementById(maxInputId);
                const minDisplay = document.getElementById(minDisplayId);
                const maxDisplay = document.getElementById(maxDisplayId);

                if (slider) {
                    slider.addEventListener('change', (evt) => {
                        const val1 = evt.detail.value1;
                        const val2 = evt.detail.value2;

                        if (minInput) minInput.value = val1;
                        if (maxInput) maxInput.value = val2;

                        if (minDisplay) minDisplay.textContent = formatFn(val1);
                        if (maxDisplay) maxDisplay.textContent = formatFn(val2);
                    });
                }
            };

            const formatCurrency = (val) => '₹' + Math.round(val).toLocaleString('en-IN');
            const formatGB = (val) => Math.round(val) + ' GB';
            const formatStorage = (val) => {
                return val < 1000 ? Math.round(val) + ' GB' : (Math.round(val) / 1024).toFixed(0) + ' TB';
            };

            attachSliderListeners('price-slider', 'min_price', 'max_price', 'price-min-display', 'price-max-display', formatCurrency);
            attachSliderListeners('ram-slider', 'min_ram', 'max_ram', 'ram-min-display', 'ram-max-display', formatGB);
            attachSliderListeners('storage-slider', 'min_storage', 'max_storage', 'storage-min-display', 'storage-max-display', formatStorage);
            
            const formatScore = (val) => Math.round(val).toLocaleString('en-IN');
            attachSliderListeners('antutu-slider', 'min_antutu', 'max_antutu', 'antutu-min-display', 'antutu-max-display', formatScore);

            // --- Apply Filters Button ---
            if (applyBtn) {
                applyBtn.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('page');
                    
                    const curMinPrice = document.getElementById('min_price');
                    const curMaxPrice = document.getElementById('max_price');
                    const curMinRam = document.getElementById('min_ram');
                    const curMaxRam = document.getElementById('max_ram');
                    const curMinStorage = document.getElementById('min_storage');
                    const curMaxStorage = document.getElementById('max_storage');
                    const curMinAntutu = document.getElementById('min_antutu');
                    const curMaxAntutu = document.getElementById('max_antutu');
                    const curBootloader = document.getElementById('bootloader');
                    const curTurnip = document.getElementById('turnip');
                    const curShowUnverified = document.getElementById('show_unverified');

                    if (curMinPrice) url.searchParams.set('min_price', curMinPrice.value);
                    if (curMaxPrice) url.searchParams.set('max_price', curMaxPrice.value);
                    if (curMinRam) url.searchParams.set('min_ram', curMinRam.value);
                    if (curMaxRam) url.searchParams.set('max_ram', curMaxRam.value);
                    if (curMinStorage) url.searchParams.set('min_storage', curMinStorage.value);
                    if (curMaxStorage) url.searchParams.set('max_storage', curMaxStorage.value);
                    if (curMinAntutu) url.searchParams.set('min_antutu', curMinAntutu.value);
                    if (curMaxAntutu) url.searchParams.set('max_antutu', curMaxAntutu.value);

                    // Collect Checkboxes (Brands & IP)
                    const brandCheckboxes = document.querySelectorAll('input[name="brands[]"]:checked');
                    brandCheckboxes.forEach(cb => url.searchParams.append('brands[]', cb.value));

                    const ipCheckboxes = document.querySelectorAll('input[name="ip_ratings[]"]:checked');
                    ipCheckboxes.forEach(cb => url.searchParams.append('ip_ratings[]', cb.value));
                    
                    if (curBootloader) {
                        if (curBootloader.checked) url.searchParams.set('bootloader', '1');
                        else url.searchParams.delete('bootloader');
                    }
                    
                    if (curTurnip) {
                        if (curTurnip.checked) url.searchParams.set('turnip', '1');
                        else url.searchParams.delete('turnip');
                    }

                    if (curShowUnverified) {
                        if (curShowUnverified.checked) url.searchParams.set('show_unverified', '1');
                        else url.searchParams.delete('show_unverified');
                    }
 
                    window.location.href = url.toString();
                });
            }

            if(resetBtn) {
                 resetBtn.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    const params = [
                        'min_price', 'max_price', 'min_ram', 'max_ram', 'min_storage', 'max_storage', 
                        'min_antutu', 'max_antutu', 'bootloader', 'turnip', 'show_unverified', 'page', 'brands[]', 'ip_ratings[]'
                    ];
                    params.forEach(p => url.searchParams.delete(p));
                    window.location.href = url.toString();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFilters);
        } else {
            initFilters();
        }
    </script>
@endsection
