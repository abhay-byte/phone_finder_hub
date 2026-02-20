@extends('admin.layout')

@section('title', 'Edit Forum Category')

@section('admin-content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Category: {{ $category->name }}</h1>
        <p class="text-gray-500 dark:text-gray-400">Update discussion category details.</p>
    </div>
    <a href="{{ route('admin.forum.categories.index') }}" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Categories
    </a>
</div>

<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm p-6 md:p-8 max-w-3xl">
    <form action="{{ route('admin.forum.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name', $category->name) }}"
                       class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-2">Current slug: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">{{ $category->slug }}</code>. Updating the name will regenerate the slug.</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-200 dark:border-white/10">

            <div class="flex justify-between items-center text-sm text-gray-500">
                <div>Created: {{ $category->created_at->format('M d, Y') }}</div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.forum.categories.index') }}" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 px-5 py-2.5 rounded-xl font-medium transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md transition-all">
                        Update Category
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
