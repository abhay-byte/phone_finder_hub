@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Hero / Search Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
                Find Value, <span class="text-indigo-600 dark:text-indigo-400">Not Hype</span>
            </h1>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500 dark:text-gray-400">
                Data-driven rankings based on performance per rupee.
            </p>
            
            <div class="mt-8 flex justify-center">
                <div class="relative w-full max-w-lg">
                    <input type="text" class="block w-full rounded-md border-0 py-3 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-slate-800 dark:ring-slate-700 dark:text-white" placeholder="Search phones, brands, chipsets...">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Quick Filters -->
            <div class="mt-4 flex justify-center gap-2 flex-wrap">
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 cursor-pointer hover:bg-indigo-100 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30">Under ₹20k</span>
                <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10 cursor-pointer hover:bg-purple-100 dark:bg-purple-400/10 dark:text-purple-400 dark:ring-purple-400/30">Gaming</span>
                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 cursor-pointer hover:bg-green-100 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30">Best Value</span>
                <span class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 cursor-pointer hover:bg-yellow-100 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/30">Flagships</span>
            </div>
        </div>

        <!-- Phones Grid -->
        <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
            @foreach($phones as $phone)
            <div class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200 hover:ring-indigo-500 hover:shadow-md transition-all dark:bg-slate-800 dark:ring-slate-700 dark:hover:ring-indigo-400">
                <div class="aspect-h-4 aspect-w-3 bg-gray-200 sm:aspect-none group-hover:opacity-75 sm:h-64 relative overflow-hidden">
                    @if($phone->image_url)
                        <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="h-full w-full object-cover object-center sm:h-full sm:w-full">
                    @else
                         <div class="flex items-center justify-center h-full w-full bg-gray-100 dark:bg-slate-700 text-gray-400">
                            <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                         </div>
                    @endif
                    
                    <!-- Value Badge -->
                    @if($phone->value_score > 0)
                    <div class="absolute top-2 right-2 rounded-md bg-emerald-500 px-2 py-1 text-xs font-bold text-white shadow-sm">
                        {{ $phone->value_score }} pts/₹1k
                    </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <a href="{{ route('phones.show', $phone) }}">
                            <span aria-hidden="true" class="absolute inset-0"></span>
                            {{ $phone->name }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $phone->brand }}</p>
                    <div class="flex flex-1 flex-col justify-end">
                        <p class="text-sm italic text-gray-500 dark:text-gray-400 mb-2">{{ $phone->platform?->chipset ?? 'Unknown Chipset' }}</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">₹{{ number_format($phone->price) }}</p>
                    </div>
                </div>
                
                <!-- Expanded details on hover (optional enhancement later) -->
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $phones->links() }}
        </div>
    </div>
</div>
@endsection
