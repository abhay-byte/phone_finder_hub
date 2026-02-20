@extends('admin.layout')

@section('title', 'Manage Forum Posts')

@section('admin-content')
<div class="mb-8 overflow-hidden">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Forum Posts</h1>
    <p class="text-gray-500 dark:text-gray-400">View and moderate community discussions.</p>
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Post details</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Stats</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posted</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-900 dark:text-white line-clamp-1 max-w-sm">{{ $post->title }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <span class="font-semibold">By:</span> {{ $post->user->name }}
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                            {{ $post->category->name }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-center gap-3">
                            <span title="Views" class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> {{ $post->views }}</span>
                            <span title="Replies" class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg> {{ $post->comments_count }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">
                        <div title="{{ $post->created_at->format('M d, Y h:i A') }}">{{ $post->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.forum.posts.show', $post) }}" class="inline-block p-2 text-teal-500 hover:bg-teal-50 dark:hover:bg-teal-500/10 rounded-lg transition-colors" title="View & Moderate">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </a>
                        
                        <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST" class="inline-block" onsubmit="return confirm('WARNING: Are you sure you want to delete this post? All replies to it will be PERMANENTLY DELETED. This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Delete">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <p>No forum posts found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($posts->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
