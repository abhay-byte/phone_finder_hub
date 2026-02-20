@extends('layouts.app')

@push('title', $post->title . ' - Forums - Phone Finder Hub')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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
                    <a href="{{ route('forum.category', $post->category->slug) }}" class="ml-1 md:ml-2 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">{{ $post->category->name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 md:ml-2 text-gray-900 dark:text-white line-clamp-1">{{ $post->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Post -->
    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 md:p-8 flex gap-4 md:gap-6">
            <!-- Author avatar -->
            <div class="shrink-0 hidden sm:block">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex items-center justify-center font-bold text-xl shadow-inner">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex sm:hidden items-center justify-center font-bold text-sm shadow-inner shrink-0">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $post->user->name }}</span>
                    @if($post->user->isSuperAdmin())
                        <span class="bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">
                            Admin
                        </span>
                    @endif
                    <span class="text-sm text-gray-500 dark:text-gray-400">&bull;</span>
                    <time class="text-sm text-gray-500 dark:text-gray-400" datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $post->created_at->format('M d, Y h:i A') }}">
                        {{ $post->created_at->diffForHumans() }}
                    </time>
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                    {{ $post->title }}
                </h1>

                <div class="prose prose-teal max-w-none dark:prose-invert prose-img:rounded-xl prose-img:shadow-sm prose-a:text-teal-600 dark:prose-a:text-teal-400">
                    {!! $post->content !!}
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-black/20 px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                {{ number_format($post->views) }} Views
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                {{ number_format($post->comments->count()) }} Replies
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            Replies <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm py-0.5 px-2.5 rounded-full">{{ $post->comments->count() }}</span>
        </h3>

        @if($post->comments->count() > 0)
            <div class="space-y-4">
                @foreach($post->comments as $reply)
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-5 md:p-6 flex gap-4 md:gap-6" id="reply-{{ $reply->id }}">
                        <div class="shrink-0 hidden sm:block">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex items-center justify-center font-bold text-lg">
                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 flex sm:hidden items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                @if($reply->user->isSuperAdmin())
                                    <span class="bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide">
                                        Admin
                                    </span>
                                @endif
                                @if($reply->user_id === $post->user_id)
                                    <span class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide">
                                        OP
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500 dark:text-gray-400">&bull;</span>
                                <time class="text-sm text-gray-500 dark:text-gray-400" datetime="{{ $reply->created_at->toIso8601String() }}" title="{{ $reply->created_at->format('M d, Y h:i A') }}">
                                    {{ $reply->created_at->diffForHumans() }}
                                </time>
                            </div>
                            
                            <div class="prose prose-sm prose-teal max-w-none dark:prose-invert">
                                {!! nl2br(e($reply->content)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl border-dashed">
                <p class="text-gray-500 dark:text-gray-400">No replies yet. Be the first to share your thoughts!</p>
            </div>
        @endif
    </div>

    <!-- Reply Form -->
    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Leave a Reply</h3>
        
        @auth
            <form action="{{ route('forum.post.reply', $post->slug) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="content" class="sr-only">Reply Content</label>
                    <textarea name="content" id="content" rows="4" required
                              class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-4 shadow-sm"
                              placeholder="Write your reply here... Markdown is generally not supported in plain replies, but plain text works well."></textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md transition-all">
                        Post Reply
                    </button>
                </div>
            </form>
        @else
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 text-center">
                <p class="text-gray-600 dark:text-gray-400 mb-4">You must be logged in to post a reply.</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg font-medium transition-colors">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 px-5 py-2 rounded-lg font-medium transition-colors">
                        Sign Up
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection
