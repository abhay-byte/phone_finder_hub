@extends('layouts.app')

@push('title', 'Forums - Phone Finder Hub')
@push('description', 'Join the discussion about the latest smartphones, recommendations, and reviews.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
            Forums
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Join the community to discuss, compare, and get buying advice for your next smartphone.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <a href="{{ route('forum.category', $category->slug) }}" class="block group">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-teal-500/30">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                {{ $category->name }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ $category->description }}
                            </p>
                        </div>
                        <div class="bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 rounded-full w-10 h-10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span>{{ $category->posts_count }} {{ Str::plural('Post', $category->posts_count) }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl border-dashed">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No categories</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for community discussions.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
