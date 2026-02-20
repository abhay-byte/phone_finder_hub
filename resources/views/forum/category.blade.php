@extends('layouts.app')

@push('title', $category->name . ' - Forums - Phone Finder Hub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-gray-500 dark:text-gray-400 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('forum.index') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Forums</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 md:ml-2 text-gray-900 dark:text-white">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
            @endif
        </div>
        
        @auth
            <a href="{{ route('forum.post.create', $category->slug) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Post
            </a>
        @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Log in to Post
            </a>
        @endauth
    </div>

    <!-- Posts List -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
        @if($posts->count() > 0)
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($posts as $post)
                    <li class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('forum.post.show', $post->slug) }}" class="block focus:outline-none">
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 truncate">
                                        {{ $post->title }}
                                    </h2>
                                </a>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <div class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $post->user->name }}</span>
                                    </div>
                                    <span class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                                    <time datetime="{{ $post->created_at->toIso8601String() }}">
                                        {{ $post->created_at->diffForHumans() }}
                                    </time>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 shrink-0 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($post->comments_count) }}</span>
                                    <span class="text-xs">Replies</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($post->views) }}</span>
                                    <span class="text-xs">Views</span>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $posts->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No posts yet</h3>
                <p class="mt-1 text-sm text-gray-500">Be the first to start a discussion in this category.</p>
                @auth
                    <div class="mt-6">
                        <a href="{{ route('forum.post.create', $category->slug) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 transition">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Post
                        </a>
                    </div>
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection
