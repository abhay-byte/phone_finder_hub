@foreach($phones as $phone)
<a href="{{ route('phones.show', $phone) }}" class="group relative bg-white dark:bg-[#1A1A1A] rounded-[1.5rem] sm:rounded-[2rem] p-3 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-white/5 flex flex-col h-full hover:-translate-y-1">
    
    <!-- Value Badge -->
    <div class="absolute top-2 right-2 sm:top-4 sm:right-4 z-10 flex flex-col items-end gap-2">
        @if($phone->value_score)
        <div class="bg-black/5 dark:bg-white/10 backdrop-blur-md px-2 py-0.5 sm:px-3 sm:py-1 rounded-full border border-black/5 dark:border-white/5">
            <span class="text-[10px] sm:text-xs font-bold text-slate-900 dark:text-white">
                {{ $phone->value_score }} <span class="text-slate-500 font-normal">pts/₹1k</span>
            </span>
        </div>
        @endif
    </div>

    <!-- Image -->
        <div class="relative w-full aspect-[4/5] mb-3 sm:mb-6 flex items-center justify-center p-2 sm:p-4 bg-slate-50 dark:bg-black/20 rounded-[1rem] sm:rounded-[1.5rem] group-hover:bg-teal-50/30 dark:group-hover:bg-teal-900/10 transition-colors">
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
        <p class="text-[10px] sm:text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wide mb-0.5 sm:mb-1">{{ $phone->brand }}</p>
        <h3 class="text-sm sm:text-xl font-bold text-slate-900 dark:text-white mb-0.5 sm:mb-1 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors leading-snug">{{ $phone->name }}</h3>
        <p class="text-base sm:text-2xl font-black text-slate-900 dark:text-white mb-2 sm:mb-4">₹{{ number_format($phone->price) }}</p>
        
            <!-- Mini Specs -->
        <div class="hidden sm:grid grid-cols-2 gap-2 text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-white/5 pt-4">
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
