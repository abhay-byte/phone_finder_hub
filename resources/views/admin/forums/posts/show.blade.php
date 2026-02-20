@extends('admin.layout')

@section('title', 'Moderate Forum Post')

@section('admin-content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Moderate Forum Post</h1>
        <p class="text-gray-500 dark:text-gray-400">View post content and manage replies.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.forum.posts.index') }}" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Posts
        </a>
        <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to delete this post? All replies to it will be PERMANENTLY DELETED.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Delete Post
            </button>
        </form>
    </div>
</div>

<!-- Main Post Content -->
<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden mb-8">
    <div class="p-6 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                Category: {{ $post->category->name }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">&bull;</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">By: <span class="font-medium text-gray-900 dark:text-white">{{ $post->user->name }}</span></span>
            <span class="text-sm text-gray-500 dark:text-gray-400">&bull;</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">Posted on {{ $post->created_at->format('M d, Y h:i A') }}</span>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $post->title }}</h2>

        <div class="prose prose-teal max-w-none dark:prose-invert prose-img:rounded-xl">
            {!! $post->content !!}
        </div>
    </div>
</div>

<!-- Replies -->
<div class="mb-4">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Replies ({{ $post->comments->count() }})</h3>
</div>

<div class="space-y-4">
    @forelse($post->comments as $reply)
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 flex justify-between gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-medium text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">&bull;</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $reply->content }}</div>
            </div>
            
            <div class="shrink-0">
                <form action="{{ route('admin.forum.comments.destroy', $reply) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reply?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Delete Reply">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-8 text-center text-gray-500 dark:text-gray-400 border-dashed">
            No replies to this post yet.
        </div>
    @endforelse
</div>

@endsection
