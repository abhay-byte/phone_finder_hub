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
            {!! $categoriesHtml !!}
</div>
@endsection
