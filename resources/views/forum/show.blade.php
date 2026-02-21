@extends('layouts.app')

@push('title', $post->title . ' - Forums - Phone Finder Hub')

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
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                </svg>
            </li>
            <li>
                <a href="{{ route('forum.category', $post->category->slug) }}" class="hover:text-teal-600 dark:hover:text-teal-400 font-medium transition-colors">{{ $post->category->name }}</a>
            </li>
            <li>
                <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                </svg>
            </li>
            <li class="font-medium text-slate-900 dark:text-white line-clamp-1" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-4">
            {{ $post->title }}
        </h1>
        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-1.5 font-medium">
                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex items-center justify-center font-bold text-[10px]">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
                <span class="text-slate-700 dark:text-slate-300">{{ $post->user->name }}</span>
            </div>
            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full shrink-0"></span>
            <div class="flex items-center gap-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <time datetime="{{ $post->created_at->toIso8601String() }}">
                    {{ $post->created_at->format('M d, Y') }}
                </time>
            </div>
            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full shrink-0"></span>
            <div class="flex items-center gap-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>{{ number_format($post->views) }} Views</span>
            </div>
            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full shrink-0"></span>
            <div class="flex items-center gap-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                <span>{{ number_format($post->comments->count()) }} Replies</span>
            </div>
        </div>
    </div>

    <!-- Main Post -->
    <div class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm mb-6 flex flex-col md:flex-row overflow-hidden">
        
        <!-- Author Sidebar -->
        <div class="bg-slate-50 dark:bg-white/[0.02] border-b md:border-b-0 md:border-r border-slate-200 dark:border-white/5 md:w-56 p-5 md:p-6 flex flex-row md:flex-col items-center md:items-start gap-4 shrink-0">
            <div class="w-12 h-12 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex items-center justify-center font-bold text-xl shadow-inner shrink-0 scale-100 hover:scale-105 transition-transform duration-300">
                {{ strtoupper(substr($post->user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0 md:w-full md:mt-2">
                <div class="font-bold text-slate-900 dark:text-white truncate md:text-wrap md:break-words text-lg">
                    {{ $post->user->name }}
                </div>
                <div class="mt-1 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 uppercase tracking-widest border border-indigo-200 dark:border-indigo-500/30">
                        Topic Starter
                    </span>
                    @if($post->user->isSuperAdmin())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-300 uppercase tracking-widest border border-teal-200 dark:border-teal-500/30">
                            Admin
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Post Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <div class="p-6 md:p-8 flex-1">
                <div class="prose prose-slate prose-teal max-w-none dark:prose-invert prose-img:rounded-xl prose-img:shadow-sm prose-a:text-teal-600 dark:prose-a:text-teal-400">
                    {!! $post->content !!}
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 dark:bg-white/[0.01] border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-500">
                <time datetime="{{ $post->created_at->toIso8601String() }}" title="{{ $post->created_at->format('M d, Y h:i A') }}">
                    Posted {{ $post->created_at->diffForHumans() }}
                </time>
                <div class="flex items-center gap-6">
                    <form action="{{ route('forum.post.upvote', $post->slug) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="font-semibold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors uppercase tracking-wider flex items-center gap-1.5 {{ $post->upvotes > 0 ? '!text-indigo-600 dark:!text-indigo-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                            </svg>
                            Upvote ({{ number_format($post->upvotes) }})
                        </button>
                    </form>
                    <a href="#reply-form" class="font-semibold text-slate-600 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Reply
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    @if($post->comments->count() > 0)
        <div class="space-y-6 mb-10">
            @foreach($post->comments as $reply)
                <div class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm flex flex-col md:flex-row overflow-hidden" id="reply-{{ $reply->id }}">
                    
                    <!-- Author Sidebar -->
                    <div class="bg-slate-50 dark:bg-white/[0.02] border-b md:border-b-0 md:border-r border-slate-200 dark:border-white/5 md:w-56 p-5 md:p-6 flex flex-row md:flex-col items-center md:items-start gap-4 shrink-0">
                        <div class="w-10 h-10 md:w-16 md:h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-lg shadow-inner shrink-0 scale-100 hover:scale-105 transition-transform duration-300">
                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0 md:w-full md:mt-2">
                            <div class="font-bold text-slate-900 dark:text-white truncate md:text-wrap md:break-words md:text-lg">
                                {{ $reply->user->name }}
                            </div>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @if($reply->user_id === $post->user_id)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 uppercase tracking-widest border border-indigo-200/50 dark:border-indigo-500/20">
                                        OP
                                    </span>
                                @endif
                                @if($reply->user->isSuperAdmin())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-300 uppercase tracking-widest border border-teal-200/50 dark:border-teal-500/20">
                                        Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Post Content -->
                    <div class="flex-1 flex flex-col min-w-0">
                        <div class="p-6 md:p-8 flex-1">
                            <div class="prose prose-sm md:prose-base prose-slate prose-teal max-w-none dark:prose-invert">
                                {!! nl2br(e($reply->content)) !!}
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50/50 dark:bg-white/[0.01] border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-500">
                            <time datetime="{{ $reply->created_at->toIso8601String() }}" title="{{ $reply->created_at->format('M d, Y h:i A') }}">
                                {{ $reply->created_at->diffForHumans() }}
                            </time>
                            <span class="text-slate-400 dark:text-slate-600">#{{ $loop->iteration }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Reply Form -->
    <div id="reply-form" class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden mt-8 md:mt-12 scroll-mt-24">
        <div class="p-6 md:p-8">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Post a Reply
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Join the conversation. Be respectful and constructive.</p>
            
            @auth
                <form action="{{ route('forum.post.reply', $post->slug) }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label for="content" class="sr-only">Reply Content</label>
                        <div class="relative">
                            <textarea name="content" id="content" rows="5" required
                                      class="block w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500/40 p-4 transition-all resize-y placeholder-slate-400 dark:placeholder-slate-500"
                                      placeholder="Write your reply here... Plain text works best."></textarea>
                            <div class="absolute bottom-3 right-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest pointer-events-none">
                                Plain Text Only
                            </div>
                        </div>
                        @error('content') <p class="text-red-500 dark:text-red-400 text-xs mt-2 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-400 text-white px-8 py-3 rounded-xl font-bold shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
                            Submit Reply
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/5 rounded-xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-200 dark:bg-white/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Login Required</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6 max-w-sm mx-auto">You must be logged in to participate in this discussion.</p>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-[#1a1c23] border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
                            Sign Up
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection
