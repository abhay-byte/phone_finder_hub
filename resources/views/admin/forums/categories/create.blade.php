@extends('admin.layout')

@section('title', 'New Forum Category')

@section('admin-content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-300 mb-2">Create Forum Category</h1>
        <p class="text-slate-500 dark:text-slate-400 transition-colors duration-300">Add a new discussion category for the community.</p>
    </div>
    <a href="{{ route('admin.forum.categories.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Categories
    </a>
</div>

<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm p-6 md:p-8 max-w-3xl transition-colors duration-300">
    <form action="{{ route('admin.forum.categories.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1 transition-colors duration-300">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                       class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm transition-colors duration-300"
                       placeholder="e.g. Android Development, iOS Rumors...">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">A slug for the URL will be automatically generated from this name.</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1 transition-colors duration-300">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm transition-colors duration-300"
                          placeholder="Brief explanation of what goes in this category...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1 transition-colors duration-300">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                           class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm transition-colors duration-300"
                           placeholder="0">
                    <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">Lower numbers appear first. Default is 0.</p>
                    @error('order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Rules Banner -->
            <div>
                <label for="rules_banner" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1 transition-colors duration-300">Rules Banner (Optional)</label>
                <textarea name="rules_banner" id="rules_banner" rows="4" 
                          class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm transition-colors duration-300"
                          placeholder="Content here will be displayed as a banner at the top of the category...">{{ old('rules_banner') }}</textarea>
                <p class="text-xs text-gray-500 dark:text-slate-500 mt-2">Useful for category-specific rules or announcements. HTML is supported.</p>
                @error('rules_banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-200 dark:border-white/5">

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.forum.categories.index') }}" class="bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-gray-300 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-slate-700 px-5 py-2.5 rounded-xl font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md transition-all">
                    Create Category
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
