@extends('admin.layout')

@section('title')
PhoneFinderHub – Add Phone
@endsection

@section('admin-content')

{{-- Header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add New Phone</h1>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Fill in the details below. Manual fields override any auto-scraped data.</p>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="mb-6 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-2xl p-5">
    <p class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2">Please fix the following errors:</p>
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li class="text-sm text-red-500 dark:text-red-300">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.phones.import') }}" method="POST" id="importForm">
    @csrf

    {{-- ── Section 1: Phone Identity ────────────────────────── --}}
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-white/5 p-6 mb-5 shadow-sm dark:shadow-none">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-500/20 border border-teal-200 dark:border-teal-500/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900 dark:text-white text-sm">Phone Identity</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="phone_name" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Phone Name <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <input type="text" name="phone_name" id="phone_name"
                       value="{{ old('phone_name') }}"
                       placeholder="e.g. Samsung Galaxy S25 Ultra"
                       class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500/40 transition-all"
                       required>
                <p class="text-xs text-slate-500 mt-1.5">Used as the primary lookup if no GSMArena URL provided.</p>
            </div>
            <div>
                <label for="gsmarena_url" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    GSMArena URL <span class="text-slate-400 dark:text-slate-600">(optional)</span>
                </label>
                <input type="url" name="gsmarena_url" id="gsmarena_url"
                       value="{{ old('gsmarena_url') }}"
                       placeholder="https://www.gsmarena.com/samsung_galaxy_s25_ultra-12345.php"
                       class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500/40 transition-all">
                <p class="text-xs text-slate-500 mt-1.5">If provided, Skip GSMArena search — use this URL directly.</p>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Manual Overrides ─────────────────────── --}}
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-white/5 p-6 mb-5 shadow-sm dark:shadow-none">
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-500/20 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900 dark:text-white text-sm">Manual Overrides</h2>
        </div>
        <p class="text-xs text-slate-500 mt-1 mb-5">Any field you fill here <strong class="text-violet-600 dark:text-violet-400 font-semibold">overrides</strong> auto-scraped data.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="price" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Price (₹)</label>
                <div class="flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-violet-500/40 focus-within:border-violet-500/40 transition-all">
                    <span class="px-3 text-slate-400 dark:text-slate-500 text-sm font-medium select-none">₹</span>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="1"
                           placeholder="79999"
                           class="flex-1 bg-transparent py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <div class="flex flex-col border-l border-slate-200 dark:border-white/10">
                        <button type="button" onclick="stepInput('price', 500)" class="px-2.5 py-1 text-slate-400 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-xs leading-none border-b border-slate-200 dark:border-white/10">▲</button>
                        <button type="button" onclick="stepInput('price', -500)" class="px-2.5 py-1 text-slate-400 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-xs leading-none">▼</button>
                    </div>
                </div>
            </div>
            <div>
                <label for="image_url" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Image URL</label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}"
                       placeholder="https://example.com/phone.png"
                       class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/40 transition-all">
                <p class="text-xs text-slate-500 mt-1.5">If set, skips automatic image fetching.</p>
            </div>
            <div>
                <label for="amazon_url" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Amazon URL</label>
                <input type="url" name="amazon_url" id="amazon_url" value="{{ old('amazon_url') }}"
                       placeholder="https://www.amazon.in/dp/..."
                       class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/40 transition-all">
            </div>
            <div>
                <label for="flipkart_url" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Flipkart URL</label>
                <input type="url" name="flipkart_url" id="flipkart_url" value="{{ old('flipkart_url') }}"
                       placeholder="https://www.flipkart.com/..."
                       class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/40 transition-all">
            </div>
        </div>
    </div>

    {{-- ── Section 3: Benchmark Overrides ───────────────────── --}}
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-white/5 p-6 mb-5 shadow-sm dark:shadow-none">
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900 dark:text-white text-sm">Benchmark Overrides</h2>
        </div>
        <p class="text-xs text-slate-500 mt-1 mb-5">Leave blank to use auto-fetched scores. Filled values <strong class="text-amber-600 dark:text-amber-400 font-semibold">override</strong> scraped benchmarks.</p>

        @php
        $benchFields = [
            ['antutu_score',      'AnTuTu v11',        'e.g. 2200000', 10000, null],
            ['dmark_score',       '3DMark WLE',        'e.g. 4500',    100,   'Wildlife Extreme'],
            ['dmark_stability',   '3DMark Stability',  'e.g. 87',      1,     '0–100 %'],
            ['geekbench_single',  'GB6 Single',        'e.g. 3200',    50,    'Geekbench 6'],
            ['geekbench_multi',   'GB6 Multi',         'e.g. 9800',    100,   'Geekbench 6'],
            ['dxomark_score',     'DXOMark',           'e.g. 157',     1,     null],
            ['phonearena_score',  'PhoneArena Cam',    'e.g. 122',     1,     null],
        ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($benchFields as [$id, $lbl, $ph, $btnStep, $hint])
            <div>
                <label for="{{ $id }}" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ $lbl }}</label>
                <div class="flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-amber-500/40 focus-within:border-amber-500/40 transition-all">
                    <input type="number" name="{{ $id }}" id="{{ $id }}" value="{{ old($id) }}" min="0" step="1"
                           placeholder="{{ $ph }}"
                           class="flex-1 bg-transparent px-3 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none w-0">
                    <div class="flex flex-col border-l border-slate-200 dark:border-white/10 flex-shrink-0">
                        <button type="button" onclick="stepInput('{{ $id }}', {{ $btnStep }})" class="px-2 py-1 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-white hover:bg-amber-100 dark:hover:bg-amber-500/10 transition-colors text-[10px] leading-none border-b border-slate-200 dark:border-white/10">▲</button>
                        <button type="button" onclick="stepInput('{{ $id }}', -{{ $btnStep }})" class="px-2 py-1 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-white hover:bg-amber-100 dark:hover:bg-amber-500/10 transition-colors text-[10px] leading-none">▼</button>
                    </div>
                </div>
                @if($hint)
                <p class="text-xs text-slate-500 dark:text-slate-600 mt-1">{{ $hint }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Section 4: Import Options ─────────────────────────── --}}
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-white/5 p-6 mb-6 shadow-sm dark:shadow-none">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700/60 border border-slate-200 dark:border-white/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900 dark:text-white text-sm">Import Options</h2>
        </div>

        <div class="space-y-2">
            @php
            $toggles = [
                ['skip_image',    'Skip automatic image fetching',          "Won't download or process phone images. Use if you've provided an Image URL above."],
                ['skip_shopping', 'Skip shopping links scraping',            "Won't fetch Amazon/Flipkart prices. Use if you've provided links above."],
                ['force',         'Force overwrite if phone already exists', 'If the phone is already in the database, update all its data.'],
            ];
            @endphp
            @foreach($toggles as [$tid, $tlabel, $tdesc])
            <label for="{{ $tid }}" class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-white/10 hover:bg-slate-50 dark:hover:bg-white/[0.02] cursor-pointer transition-all group">
                {{-- Toggle switch --}}
                <div class="relative flex-shrink-0 mt-0.5">
                    <input type="checkbox" name="{{ $tid }}" id="{{ $tid }}" value="1" {{ old($tid) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-300 dark:bg-slate-700 peer-checked:bg-teal-500 rounded-full transition-colors duration-200 border border-slate-200 dark:border-white/10 peer-checked:border-teal-400/50"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $tlabel }}</span>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $tdesc }}</p>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="flex items-center gap-4">
        <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-400 active:scale-95 text-white font-semibold px-8 py-3.5 rounded-xl transition-all shadow-lg shadow-teal-500/20 hover:shadow-teal-500/40 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Start Import
        </button>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">Cancel</a>
    </div>
</form>

<script>
function stepInput(id, delta) {
    const el = document.getElementById(id);
    const min = parseFloat(el.min ?? '-Infinity');
    const max = parseFloat(el.max ?? 'Infinity');
    const cur = parseFloat(el.value) || 0;
    el.value = Math.max(min, Math.min(max, cur + delta));
    el.dispatchEvent(new Event('input'));
}

document.getElementById('importForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Importing...`;
});

document.getElementById('image_url').addEventListener('input', function() {
    document.getElementById('skip_image').checked = this.value.trim() !== '';
});
function updateSkipShopping() {
    const amazon = document.getElementById('amazon_url').value.trim();
    const flipkart = document.getElementById('flipkart_url').value.trim();
    if (amazon && flipkart) document.getElementById('skip_shopping').checked = true;
}
document.getElementById('amazon_url').addEventListener('input', updateSkipShopping);
document.getElementById('flipkart_url').addEventListener('input', updateSkipShopping);
</script>

@endsection
