@extends('admin.layout')

@section('admin-content')

{{-- Page Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-slate-400 mt-1">Overview of the PhoneFinderHub database</p>
    </div>
    <a href="{{ route('admin.phones.add') }}" class="bg-teal-500 hover:bg-teal-400 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/40 transition-all flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Phone
    </a>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
    <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl bg-teal-500/15 border border-teal-500/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-3xl font-black text-white">{{ $totalPhones }}</p>
            <p class="text-sm text-slate-400">Total Phones</p>
        </div>
    </div>
    <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl bg-violet-500/15 border border-violet-500/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
            <p class="text-3xl font-black text-white">{{ $latestPhones->count() }}</p>
            <p class="text-sm text-slate-400">Recent Additions</p>
        </div>
    </div>
    <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <p class="text-3xl font-black text-white">{{ \App\Models\Phone::where('expert_score', '>', 0)->count() }}</p>
            <p class="text-sm text-slate-400">Scored Phones</p>
        </div>
    </div>
</div>

{{-- Recent Phones --}}
<div class="bg-slate-900/60 rounded-2xl border border-white/5 overflow-hidden">
    <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
        <h2 class="font-semibold text-white text-sm">Recently Added Phones</h2>
    </div>
    <div class="divide-y divide-white/5">
        @forelse($latestPhones as $phone)
        <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-slate-800 border border-white/5 overflow-hidden flex items-center justify-center">
                @if($phone->image_url)
                    <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="w-full h-full object-contain">
                @else
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $phone->brand }} {{ $phone->name }}</p>
                <p class="text-xs text-slate-500">₹{{ number_format($phone->price) }} · Expert Score: {{ $phone->expert_score ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($phone->expert_score > 0)
                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-teal-500/15 text-teal-400 border border-teal-500/20">{{ $phone->expert_score }}</span>
                @endif
                <a href="{{ route('admin.phones.edit', $phone) }}"
                   class="p-1.5 rounded-lg text-slate-500 hover:text-teal-400 hover:bg-teal-500/10 transition-all"
                   title="Edit Phone">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <a href="{{ route('phones.show', $phone) }}" target="_blank"
                   class="p-1.5 rounded-lg text-slate-500 hover:text-white hover:bg-white/5 transition-all"
                   title="View Phone">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center text-slate-500 text-sm">
            No phones added yet.
            <a href="{{ route('admin.phones.add') }}" class="text-teal-400 hover:text-teal-300 ml-1">Add one now →</a>
        </div>
        @endforelse
    </div>
</div>

@endsection
