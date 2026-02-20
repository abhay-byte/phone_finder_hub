@extends('admin.layout')

@section('admin-title', 'Manage Comments')

@section('admin-content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-teal-50 dark:bg-teal-500/10 rounded-xl text-teal-600 dark:text-teal-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                </div>
                Comments Management
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View, reply to, and delete user comments across all phones.</p>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm border border-slate-200 dark:border-white/5 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.comments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Search Content</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search comments..." 
                       class="w-full rounded-xl border-slate-200 dark:border-white/10 dark:bg-slate-800 focus:ring-teal-500 focus:border-teal-500 text-sm py-2 px-3 text-slate-900 dark:text-slate-100 placeholder-slate-400">
            </div>

            <div>
                <label for="phone_id" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Filter by Phone</label>
                <select name="phone_id" id="phone_id" class="w-full rounded-xl border-slate-200 dark:border-white/10 dark:bg-slate-800 focus:ring-teal-500 focus:border-teal-500 text-sm py-2 px-3 text-slate-900 dark:text-slate-100 cursor-pointer">
                    <option value="">All Phones</option>
                    @foreach($phones as $phone)
                        <option value="{{ $phone->id }}" {{ request('phone_id') == $phone->id ? 'selected' : '' }}>
                            {{ $phone->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="sort" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sort</label>
                    <select name="sort" id="sort" class="w-full rounded-xl border-slate-200 dark:border-white/10 dark:bg-slate-800 focus:ring-teal-500 focus:border-teal-500 text-sm py-2 px-3 text-slate-900 dark:text-slate-100 cursor-pointer">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="most_upvoted" {{ request('sort') == 'most_upvoted' ? 'selected' : '' }}>Most Upvoted</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors h-[42px]">
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Comments Table -->
    <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm border border-slate-200 dark:border-white/5 rounded-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-medium">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-xl truncate">Author</th>
                        <th class="px-6 py-4 min-w-[300px]">Content</th>
                        <th class="px-6 py-4 truncate">Phone</th>
                        <th class="px-6 py-4 text-center">Score</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right rounded-tr-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($comments as $comment)
                        <tr x-data="{ replying: false }" class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                            
                            <!-- Author -->
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-teal-500 to-emerald-500 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ substr($comment->user->username ?? 'Anonymous', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white text-sm max-w-[120px] truncate" title="{{ $comment->user->username ?? 'Anonymous' }}">
                                            {{ $comment->user->username ?? 'Anonymous' }}
                                        </div>
                                        @if($comment->parent_id)
                                            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded font-medium truncate inline-block max-w-[120px]">
                                                Reply
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Content Area -->
                            <td class="px-6 py-4 align-top text-slate-600 dark:text-slate-300">
                                <p class="whitespace-pre-wrap break-words text-sm">{{ $comment->content }}</p>

                                <!-- Inline Reply Form -->
                                <div x-show="replying" x-collapse x-cloak class="mt-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-200 dark:border-white/10">
                                    <form action="{{ route('admin.comments.reply', $comment) }}" method="POST">
                                        @csrf
                                        <textarea name="content" rows="2" 
                                                  class="w-full text-sm rounded-lg border-slate-200 dark:border-white/10 dark:bg-slate-900 focus:ring-teal-500 focus:border-teal-500 text-slate-900 dark:text-white placeholder-slate-400 mb-2" 
                                                  placeholder="Write an admin reply..." required></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="replying = false" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 font-medium transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-teal-600 text-white font-medium hover:bg-teal-700 transition-colors shadow-sm shadow-teal-500/20">
                                                Post Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="px-6 py-4 align-top">
                                <a href="{{ route('phones.show', $comment->phone) }}#comments" target="_blank" 
                                   class="text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-medium hover:underline flex items-center gap-1">
                                    <span class="max-w-[150px] truncate" title="{{ $comment->phone->name }}">{{ $comment->phone->name }}</span>
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </td>

                            <!-- Score -->
                            <td class="px-6 py-4 align-top text-center text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap">
                                @if($comment->upvotes_count > 0)
                                    <span class="text-orange-600 dark:text-orange-400">+{{ $comment->upvotes_count }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 align-top text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $comment->created_at->format('M j, Y') }}<br>
                                <span class="text-xs">{{ $comment->created_at->format('g:i A') }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 align-top text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="replying = !replying" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-500/10 transition-colors tooltip-trigger" title="Reply">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    </button>
                                    
                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment? Replies will be orphaned.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors tooltip-trigger" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <p class="text-base font-medium">No comments found</p>
                                <p class="text-sm mt-1">Try adjusting your filters or search terms.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($comments->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-slate-800/30">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
