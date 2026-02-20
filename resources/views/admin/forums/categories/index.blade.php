@extends('admin.layout')

@section('title', 'Manage Forum Categories')

@section('admin-content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Forum Categories</h1>
        <p class="text-gray-500 dark:text-gray-400">Manage discussion categories for the community forum.</p>
    </div>
    <a href="{{ route('admin.forum.categories.create') }}" class="inline-flex justify-center items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm transition-colors w-full sm:w-auto">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Category
    </a>
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posts</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-1.5 py-0.5 inline-block mt-1 bg-gray-50 dark:bg-gray-800">
                            /c/{{ $category->slug }}
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400">
                            {{ number_format($category->posts_count) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">
                        {{ $category->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.forum.categories.edit', $category) }}" class="inline-flex p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </a>
                            
                            <form action="{{ route('admin.forum.categories.destroy', $category) }}" method="POST" class="m-0" onsubmit="return confirm('WARNING: Are you sure you want to delete this category? All posts and comments inside it will be PERMANENTLY DELETED. This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <p>No forum categories found.</p>
                            <a href="{{ route('admin.forum.categories.create') }}" class="text-teal-600 hover:text-teal-700 dark:text-teal-400 mt-2 font-medium">Create one now &rarr;</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($categories->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
