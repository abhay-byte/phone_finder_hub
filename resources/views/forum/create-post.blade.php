@extends('layouts.app')

@push('title', 'New Post in ' . $category->name . ' - Forums - Phone Finder Hub')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Tailwind/Quill adjustments */
    .ql-toolbar.ql-snow {
        border-color: #e5e7eb;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        background-color: #f9fafb;
    }
    .dark .ql-toolbar.ql-snow {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: rgba(255, 255, 255, 0.05);
    }
    .ql-container.ql-snow {
        border-color: #e5e7eb;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        background-color: transparent;
        font-family: inherit;
        font-size: 1rem;
    }
    .dark .ql-container.ql-snow {
        border-color: rgba(255, 255, 255, 0.1);
        color: white;
    }
    .ql-editor {
        min-height: 300px;
    }
    /* Toolbar icon colors for dark mode */
    .dark .ql-snow .ql-stroke { stroke: #9ca3af; }
    .dark .ql-snow .ql-fill { fill: #9ca3af; }
    .dark .ql-snow .ql-picker { color: #9ca3af; }
</style>
@endpush

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
                    <a href="{{ route('forum.category', $category->slug) }}" class="ml-1 md:ml-2 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">{{ $category->name }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 md:ml-2 text-gray-900 dark:text-white">New Post</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Post</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Start a discussion in {{ $category->name }}</p>
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 md:p-8">
        <form action="{{ route('forum.post.store', $category->slug) }}" method="POST" id="postForm">
            @csrf
            
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                       class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm"
                       placeholder="What's on your mind?">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Rich Text Content -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Post Content <span class="text-red-500">*</span></label>
                <input type="hidden" name="content" id="content">
                <div id="editor">{!! old('content') !!}</div>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-2">You can use the toolbar to embed images using URLs.</p>
            </div>

            <!-- Submit buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('forum.category', $category->slug) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md transition-all">
                    Publish Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image'],
                        ['clean']
                    ],
                    handlers: {
                        image: imageHandler
                    }
                }
            },
            placeholder: 'Type your message here...',
        });

        // Sync content
        var form = document.querySelector('#postForm');
        form.onsubmit = function() {
            var content = document.querySelector('input[name=content]');
            var html = quill.root.innerHTML;
            if(html === '<p><br></p>') html = ''; // handle empty
            content.value = html;
        };

        // Simple prompt for images instead of full upload for normal users
        function imageHandler() {
            var range = this.quill.getSelection();
            var value = prompt('What is the image URL?');
            if(value) {
                this.quill.insertEmbed(range.index, 'image', value, Quill.sources.USER);
            }
        }
    });
</script>
@endpush
