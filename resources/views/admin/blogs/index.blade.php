@extends('admin.layout')

@section('title', 'Manage Blogs')

@section('admin-content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Manage Blogs</h1>
        <p class="text-gray-500 dark:text-gray-400">Write, edit, and publish your tech news and phone reviews.</p>
    </div>
    
    <div class="flex items-center gap-4 w-full md:w-auto">
        <form action="{{ route('admin.blogs.index') }}" method="GET" class="relative flex-1 md:w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search blogs..." 
                   class="w-full bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block pl-10 p-2.5 shadow-sm">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            @if(request('search'))
                <a href="{{ route('admin.blogs.index') }}" class="absolute right-3 top-3 text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
        <a href="{{ route('admin.blogs.create') }}" class="flex-shrink-0 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Write Blog
        </a>
    </div>
</div>

<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50/50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider border-l border-gray-200 dark:border-white/5">Author</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider border-l border-gray-200 dark:border-white/5">Status</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider border-l border-gray-200 dark:border-white/5">Date</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $blog)
                    <tr class="bg-white dark:bg-transparent border-b border-gray-50 dark:border-white/5 hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                @if($blog->featured_image)
                                    <img src="{{ $blog->featured_image }}" alt="Cover" class="w-12 h-12 rounded-lg object-cover bg-gray-100 dark:bg-white/5">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div class="max-w-[200px] md:max-w-xs">
                                    <div class="font-bold text-gray-900 dark:text-white truncate" title="{{ $blog->title }}">{{ $blog->title }}</div>
                                    <div class="text-xs text-gray-400 truncate mt-1">{{ $blog->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle font-medium border-l border-gray-100 dark:border-white/5">
                            {{ $blog->author->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 align-middle border-l border-gray-100 dark:border-white/5">
                            @if($blog->is_published)
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/50">Published</span>
                            @else
                                <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 text-xs font-bold px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-800/50">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle font-medium border-l border-gray-100 dark:border-white/5 whitespace-nowrap">
                            <div class="text-gray-900 dark:text-white">{{ $blog->created_at->format('M j, Y') }}</div>
                            @if($blog->is_published && $blog->published_at)
                                <div class="text-xs text-gray-400 mt-1">Pub: {{ $blog->published_at->format('M j, Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($blog->is_published)
                                    <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors" title="View Public Post">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @endif
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/40 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-white/5">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                            <p class="font-medium text-lg text-gray-900 dark:text-white mb-1">No blogs written yet</p>
                            <p class="text-sm mb-4">Start sharing your thoughts, news, and reviews.</p>
                            <a href="{{ route('admin.blogs.create') }}" class="inline-flex bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Write First Blog
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($blogs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            {{ $blogs->links() }}
        </div>
    @endif
</div>
@endsection
