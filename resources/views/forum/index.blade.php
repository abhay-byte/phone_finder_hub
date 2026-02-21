@extends('layouts.app')

@push('title', 'Forums - Phone Finder Hub')
@push('description', 'Join the discussion about the latest smartphones, recommendations, and reviews.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 md:mb-12">
        <div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-indigo-600 dark:from-teal-400 dark:to-indigo-400 tracking-tight">
                Community Forums
            </h1>
            <p class="mt-3 text-lg text-slate-600 dark:text-slate-400 max-w-2xl">
                Join the conversation. Discuss, compare, and get buying advice for your next smartphone with tech enthusiasts worldwide.
            </p>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
            </span>
            <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Active Community</span>
        </div>
    </div>

    <!-- Categories List -->
    <div class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden">
        <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-4 bg-slate-50 dark:bg-white/[0.02] border-b border-slate-200 dark:border-white/5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
            <div class="col-span-12 sm:col-span-8 lg:col-span-9">Category</div>
            <div class="col-span-12 sm:col-span-4 lg:col-span-3 text-right">Statistics</div>
        </div>

        <ul class="divide-y divide-slate-100 dark:divide-white/5">
            @forelse($categories as $category)
                <li class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-300">
                    <a href="{{ route('forum.category', $category->slug) }}" class="block px-6 py-5 md:py-6">
                        <div class="grid grid-cols-12 gap-4 sm:items-center">
                            
                            <!-- Category Info -->
                            <div class="col-span-12 sm:col-span-8 lg:col-span-9 flex items-start gap-4 md:gap-5">
                                <div class="shrink-0 mt-1 md:mt-0">
                                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-gradient-to-br from-teal-500/10 to-indigo-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 group-hover:from-teal-500 group-hover:to-indigo-500 group-hover:text-white transition-all duration-300 shadow-sm">
                                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors truncate">
                                        {{ $category->name }}
                                    </h2>
                                    <p class="mt-1 md:mt-1.5 text-sm text-slate-500 dark:text-slate-400 line-clamp-2 md:line-clamp-none pr-4 md:pr-8 leading-relaxed">
                                        {{ $category->description }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Category Stats -->
                            <div class="col-span-12 sm:col-span-4 lg:col-span-3 flex sm:justify-end mt-3 sm:mt-0 pl-16 sm:pl-0">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5 group-hover:bg-white dark:group-hover:bg-white/10 group-hover:shadow-sm group-hover:border-teal-200 dark:group-hover:border-teal-500/30 transition-all duration-300">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ number_format($category->posts_count) }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide ml-0.5">Posts</span>
                                </div>
                            </div>
                            
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-6 py-16 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">No categories found</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Our forums are currently being set up. Check back soon to join the conversation.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
