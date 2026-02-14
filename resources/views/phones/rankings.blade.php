@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-teal-500 selection:text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Smartphone Rankings</h1>
                <p class="text-slate-600 dark:text-slate-400 font-medium">
                    Detailed performance & value analysis
                </p>
            </div>
            
            <!-- Tabs -->
            <div id="tabs-container" class="bg-gray-200 dark:bg-white/10 p-1.5 rounded-xl inline-flex font-bold text-sm animate-fade-in-up delay-200">
                <a href="{{ route('phones.rankings', ['tab' => 'ueps']) }}" 
                   class="px-4 py-2 rounded-lg transition-all {{ $tab == 'ueps' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                   UEPS 4.0
                </a>
                <a href="{{ route('phones.rankings', ['tab' => 'performance']) }}" 
                   class="px-4 py-2 rounded-lg transition-all {{ $tab == 'performance' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                   Performance
                </a>
                <a href="{{ route('phones.rankings', ['tab' => 'value']) }}" 
                   class="px-4 py-2 rounded-lg transition-all {{ $tab == 'value' ? 'bg-white dark:bg-black shadow-sm text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                   Value
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div id="rankings-table-container" class="bg-white dark:bg-[#121212] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 overflow-hidden animate-fade-in-up delay-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                            @if($tab == 'ueps')
                                <!-- UEPS Info Card -->
                                <th colspan="5" class="p-0 border-b-0 animate-fade-in-up delay-500">
                                    <div class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                        <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-teal-500/30 transition-colors duration-500"></div>
                                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold border border-teal-500/30">Methodology</span>
                                                    <h3 class="text-xl font-bold text-white">What is UEPS 4.0?</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>Ultra-Extensive Phone Scoring System (UEPS-40)</strong> evaluates devices on a 200-point scale across 40+ touchpoints, including real-world build quality, display efficiency, sustained performance, and camera versatility.
                                                </p>
                                            </div>
                                            <a href="{{ route('ueps.methodology') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Methodology
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @elseif($tab == 'performance')
                                <!-- FPI Info Card -->
                                <th colspan="8" class="p-0 border-b-0 animate-fade-in-up delay-500">
                                    <div class="m-5 p-6 bg-zinc-900 dark:bg-white/5 rounded-2xl text-white relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                                         <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-blue-500/30 transition-colors duration-500"></div>
                                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                            <div>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-500/30">Methodology</span>
                                                    <h3 class="text-xl font-bold text-white">What is FPI?</h3>
                                                </div>
                                                <p class="text-slate-300 text-sm max-w-xl">
                                                    The <strong>Final Performance Index (FPI)</strong> is a weighted metric combining <strong>AnTuTu v11 (40%)</strong>, <strong>Geekbench (40%)</strong>, and <strong>3DMark (20%)</strong> scores to provide a single, normalized performance rating.
                                                </p>
                                            </div>
                                            <a href="{{ route('fpi.methodology') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:scale-105 active:scale-95 transition-all">
                                                View Formula
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            @endif
                        </tr>
                        <tr class="bg-slate-50/50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                            <th class="px-6 py-4 sticky left-0 bg-slate-50/50 dark:bg-[#181818] z-10 w-16 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 sticky left-16 bg-slate-50/50 dark:bg-[#181818] z-10 text-xs font-bold text-slate-500 uppercase tracking-wider">Phone</th>
                            
                            <!-- Common: Price -->
                            <th class="px-6 py-4 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price', 'direction' => $sort == 'price' && $direction == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1">
                                    Price
                                    @if($sort == 'price')
                                        <span class="text-teal-500">{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="opacity-0 group-hover:opacity-30">↓</span>
                                    @endif
                                </a>
                            </th>

                            @if($tab == 'ueps')
                                <!-- UEPS Columns -->
                                <th class="px-6 py-4 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'ueps_score', 'direction' => $sort == 'ueps_score' && $direction == 'desc' ? 'asc' : 'desc']) }}" class="flex items-center justify-end gap-1 text-teal-600 dark:text-teal-400">
                                        UEPS 4.0
                                        @if($sort == 'ueps_score')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_ueps', 'direction' => $sort == 'price_per_ueps' && $direction == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1">
                                        Price / Point
                                        @if($sort == 'price_per_ueps')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                            @elseif($tab == 'performance')
                                <!-- Performance Columns -->
                                <th class="p-5 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-right">
                                    <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'overall_score', 'direction' => $sort == 'overall_score' && $direction == 'desc' ? 'asc' : 'desc']) }}" class="flex items-center justify-end gap-1 text-blue-600 dark:text-blue-400">
                                        FPI Score
                                        @if($sort == 'overall_score')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                     <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'price_per_fpi', 'direction' => $sort == 'price_per_fpi' && $direction == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1">
                                        Price / Point
                                        @if($sort == 'price_per_fpi')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="p-5 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-right">
                                     <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'antutu_score', 'direction' => $sort == 'antutu_score' && $direction == 'desc' ? 'asc' : 'desc']) }}" class="flex items-center justify-end gap-1">
                                        AnTuTu
                                        @if($sort == 'antutu_score')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="p-5 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors text-right">
                                     <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'geekbench_multi', 'direction' => $sort == 'geekbench_multi' && $direction == 'desc' ? 'asc' : 'desc']) }}" class="flex items-center justify-end gap-1">
                                        Geekbench
                                        @if($sort == 'geekbench_multi')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="p-5 text-right">3DMark</th>
                            @elseif($tab == 'value')
                                <!-- Value Columns -->
                                <th class="p-5 cursor-pointer hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group text-right">
                                    <a href="{{ route('phones.rankings', ['tab' => $tab, 'sort' => 'value_score', 'direction' => $sort == 'value_score' && $direction == 'desc' ? 'asc' : 'desc']) }}" class="flex items-center justify-end gap-1 text-emerald-600 dark:text-emerald-400">
                                        Value Score
                                        @if($sort == 'value_score')
                                            <span>{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="p-5 text-right">FPI</th>
                                <th class="p-5 text-right">UEPS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-medium text-slate-700 dark:text-slate-300">
                        @foreach($phones as $index => $phone)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group animate-stagger-fade-in" style="animation-delay: {{ ($index * 50) + 300 }}ms;">
                            <td class="px-6 py-5 sticky left-0 bg-white dark:bg-[#121212] group-hover:bg-slate-50 dark:group-hover:bg-[#181818] text-center font-bold text-slate-400">
                                #{{ $ranks[$phone->id] ?? '-' }}
                            </td>
                            <td class="px-6 py-5 sticky left-16 bg-white dark:bg-[#121212] group-hover:bg-slate-50 dark:group-hover:bg-[#181818]">
                                <a href="{{ route('phones.show', $phone) }}" class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-white/5 rounded-xl flex items-center justify-center p-1.5 border border-slate-200 dark:border-white/5">
                                        @if($phone->image_url)
                                            <img src="{{ $phone->image_url }}" alt="" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-base leading-tight">{{ $phone->name }}</div>
                                        <div class="text-xs text-slate-500 font-normal">{{ $phone->model_variant }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-5 font-mono text-slate-600 dark:text-slate-400">₹{{ number_format($phone->price) }}</td>

                            @if($tab == 'ueps')
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 font-bold border border-teal-100 dark:border-teal-800">
                                        {{ $phone->ueps_score ?? '-' }}
                                        <span class="text-[10px] opacity-60 font-normal">/200</span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-mono text-slate-500">
                                     ₹{{ $phone->ueps_score > 0 ? number_format($phone->price / $phone->ueps_score) : '-' }}
                                </td>
                            @elseif($tab == 'performance')
                                <td class="p-5 text-right">
                                     <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 font-bold border border-blue-100 dark:border-blue-800">
                                        {{ $phone->overall_score }}
                                        <span class="text-[10px] opacity-60 font-normal">/100</span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-mono text-slate-500">
                                     ₹{{ $phone->overall_score > 0 ? number_format($phone->price / $phone->overall_score) : '-' }}
                                </td>
                                <td class="p-5 text-right font-mono {{ $phone->benchmarks && $phone->benchmarks->antutu_score > 2000000 ? 'text-green-600 dark:text-green-400 font-bold' : '' }}">
                                    {{ $phone->benchmarks ? number_format($phone->benchmarks->antutu_score) : '-' }}
                                </td>
                                <td class="p-5 text-right font-mono">
                                    {{ $phone->benchmarks ? number_format($phone->benchmarks->geekbench_multi) : '-' }}
                                </td>
                                <td class="p-5 text-right font-mono text-orange-600 dark:text-orange-400">
                                    {{ $phone->benchmarks ? number_format($phone->benchmarks->dmark_wild_life_extreme ?? 0) : '-' }}
                                </td>
                            @elseif($tab == 'value')
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 font-bold text-base border border-emerald-200 dark:border-emerald-800">
                                        {{ $phone->value_score }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-mono font-bold text-blue-600 dark:text-blue-400">
                                    {{ $phone->overall_score }}
                                </td>
                                <td class="px-6 py-5 text-right font-mono font-bold text-teal-600 dark:text-teal-400">
                                    {{ $phone->ueps_score ?? '-' }}
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-slate-50 dark:bg-white/5 border-t border-slate-200 dark:border-white/5 p-4">
                {{ $phones->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="bg-gray-50 dark:bg-black min-h-screen py-12 pt-24 font-sans selection:bg-teal-500 selection:text-white">
    <!-- ... (rest of content) ... -->
@endsection
