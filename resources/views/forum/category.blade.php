@extends('layouts.app')

@push('title', $category->name . ' - Forums - Phone Finder Hub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('forum.index') }}" class="hover:text-teal-600 dark:hover:text-teal-400 font-medium transition-colors">Forums</a>
            </li>
            <li>
                <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </li>
            <li class="font-medium text-slate-900 dark:text-white" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 md:mb-12">
        <div class="flex-1 min-w-0">
            <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-indigo-600 dark:from-teal-400 dark:to-indigo-400 tracking-tight">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="mt-3 text-lg text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
        </div>
        
        <div class="shrink-0">
            @auth
                <a href="{{ route('forum.post.create', $category->slug) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-transparent shadow-sm hover:shadow-md text-sm font-bold rounded-xl text-white bg-teal-600 hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-400 transition-all hover:-translate-y-0.5">
                    <svg class="w-5 h-5 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Discussion
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-300 dark:border-slate-600 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-white dark:bg-[#1a1c23] hover:bg-slate-50 dark:hover:bg-slate-800 transition-all hover:border-teal-500 dark:hover:border-teal-400 hover:text-teal-600 shadow-sm hover:shadow">
                    Log in to Post
                </a>
            @endauth
        </div>
    </div>

    <!-- Category Rules Banner -->
    @if($category->rules_banner)
        <div class="mb-8 p-6 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded-r-xl shadow-sm prose prose-sm prose-indigo dark:prose-invert max-w-none">
            {!! $category->rules_banner !!}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Discussions</h2>
        <div class="flex flex-wrap items-center gap-2 text-sm w-full sm:w-auto">
            <span class="text-slate-500 dark:text-slate-400 font-medium mr-1 hidden sm:inline">Sort by:</span>
            @php
                $sortOptions = [
                    'latest' => 'Latest',
                    'upvotes' => 'Most Upvotes',
                    'views' => 'Most Viewed',
                    'comments' => 'Most Replies',
                    'updated' => 'Recently Updated',
                    'oldest' => 'Oldest',
                ];
                $currentSort = request('sort', 'latest');
            @endphp
            @foreach($sortOptions as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border {{ $currentSort === $key ? 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/20' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-[#1a1c23] dark:text-slate-400 dark:border-white/10 dark:hover:bg-white/5' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Posts List -->
    <div class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden mb-8">
        @if($posts->count() > 0)
            <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-4 bg-slate-50 dark:bg-white/[0.02] border-b border-slate-200 dark:border-white/5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <div class="col-span-12 sm:col-span-8 lg:col-span-9">Topic</div>
                <div class="col-span-12 sm:col-span-4 lg:col-span-3 text-right">Statistics</div>
            </div>

            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach($posts as $post)
                    <li class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-300">
                        <div class="px-6 py-5">
                            <div class="grid grid-cols-12 gap-4 sm:items-center">
                                
                                <div class="col-span-12 sm:col-span-8 lg:col-span-9 flex items-start sm:items-center gap-4">
                                    <div class="shrink-0 hidden sm:block">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-inner shrink-0 group-hover:scale-105 transition-transform">
                                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('forum.post.show', $post->slug) }}" class="block focus:outline-none">
                                            <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors truncate">
                                                {{ $post->title }}
                                            </h2>
                                        </a>
                                        <div class="mt-1.5 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                            <div class="flex items-center gap-1.5 font-medium">
                                                <div class="w-4 h-4 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 flex sm:hidden items-center justify-center font-bold text-[8px]">
                                                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                                </div>
                                                <span class="text-slate-700 dark:text-slate-300">{{ $post->user->name }}</span>
                                            </div>
                                            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full shrink-0"></span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <time datetime="{{ $post->created_at->toIso8601String() }}">
                                                    {{ $post->created_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-12 sm:col-span-4 lg:col-span-3 flex justify-start sm:justify-end gap-5 sm:gap-6 mt-3 sm:mt-0 pl-14 sm:pl-0">
                                    <div class="flex flex-col items-center {{ $post->upvotes > 0 ? 'text-teal-600 dark:text-teal-400' : '' }}">
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 {{ $post->upvotes > 0 ? '!text-teal-600 dark:text-teal-400' : '' }}">{{ number_format($post->upvotes) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5 {{ $post->upvotes > 0 ? '!text-teal-600 dark:text-teal-400' : '' }}">Upvotes</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ number_format($post->comments_count) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Replies</span>
                                    </div>
                                    <div class="flex flex-col items-center opacity-70">
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ number_format($post->views) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Views</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-6 py-20 text-center">
                <div class="w-20 h-20 mx-auto bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-5">
                    <svg class="h-10 w-10 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No discussions yet</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm mx-auto">It's a bit quiet here. Be the first to start a conversation in this category.</p>
                @auth
                    <a href="{{ route('forum.post.create', $category->slug) }}" class="inline-flex items-center gap-2 px-6 py-3 border border-transparent shadow hover:shadow-lg text-sm font-bold rounded-xl text-white bg-teal-600 hover:bg-teal-700 transition-all hover:-translate-y-0.5">
                        <svg class="w-5 h-5 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Start the first discussion
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 border border-slate-300 dark:border-slate-600 text-sm font-medium rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-[#1a1c23] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Log in to start a discussion
                    </a>
                @endauth
            </div>
        @endif
    </div>
    
    @if($posts->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
