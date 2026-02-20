@extends('admin.layout')

@section('admin-content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white">All Phones</h1>
        <p class="text-slate-400 text-sm mt-1">Manage complete device database</p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
        <form action="{{ route('admin.phones.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search phones..." 
                   class="bg-slate-900 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-500/40 w-full sm:w-64 placeholder-slate-500 transition-all">
            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </form>
        <a href="{{ route('admin.phones.add') }}" class="bg-teal-500 hover:bg-teal-400 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/40 transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Phone
        </a>
    </div>
</div>

<div class="bg-slate-900/60 rounded-2xl border border-white/5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.02] border-b border-white/5 text-xs uppercase text-slate-400 font-semibold tracking-wider">
                    <th class="px-6 py-4">Phone</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Score</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                @forelse($phones as $phone)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-slate-800 border border-white/5 overflow-hidden flex items-center justify-center">
                                @if($phone->image_url)
                                    <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-white">{{ $phone->brand }} {{ $phone->name }}</div>
                                <div class="text-xs text-slate-500">ID: {{ $phone->id }} • {{ $phone->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-300">
                        ₹{{ number_format($phone->price) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($phone->expert_score > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-500/10 text-teal-400 border border-teal-500/20">
                            {{ $phone->expert_score }}
                        </span>
                        @else
                        <span class="text-slate-600 text-xs italic">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.phones.edit', $phone) }}" 
                               class="p-2 rounded-lg text-slate-400 hover:text-teal-400 hover:bg-teal-500/10 transition-all" 
                               title="Edit Phone">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <a href="{{ route('phones.show', $phone) }}" target="_blank"
                               class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all"
                               title="View Live">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        No phones found in the database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($phones->hasPages())
    <div class="px-6 py-4 border-t border-white/5">
        {{ $phones->links() }}
    </div>
    @endif
</div>
@endsection
