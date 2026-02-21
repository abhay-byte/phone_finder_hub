@extends('layouts.app')

@push('title', 'New Post in ' . $category->name . ' - Forums - Phone Finder Hub')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Tailwind/Quill adjustments */
    .ql-toolbar.ql-snow {
        border-color: #e2e8f0; /* slate-200 */
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        background-color: #f8fafc; /* slate-50 */
        padding: 0.75rem;
    }
    .dark .ql-toolbar.ql-snow {
        border-color: rgba(255, 255, 255, 0.05);
        background-color: rgba(255, 255, 255, 0.02);
    }
    .ql-container.ql-snow {
        border-color: #e2e8f0;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        background-color: transparent;
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .dark .ql-container.ql-snow {
        border-color: rgba(255, 255, 255, 0.05);
        color: white;
    }
    
    /* Focus states */
    .ql-container.ql-snow.focus {
        border-color: rgba(20, 184, 166, 0.4); /* teal-500/40 */
        box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.4);
    }
    
    .ql-editor {
        min-height: 350px;
        padding: 1.25rem;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8; /* slate-400 */
        font-style: normal;
    }
    .dark .ql-editor.ql-blank::before {
        color: #64748b; /* slate-500 */
    }
    
    /* Toolbar icon colors for dark mode */
    .dark .ql-snow .ql-stroke { stroke: #94a3b8; }
    .dark .ql-snow .ql-fill { fill: #94a3b8; }
    .dark .ql-snow .ql-picker { color: #94a3b8; }
    .dark .ql-snow .ql-picker-options { background-color: #1e293b; border-color: rgba(255, 255, 255, 0.1); }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
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
                <a href="{{ route('forum.category', $category->slug) }}" class="hover:text-teal-600 dark:hover:text-teal-400 font-medium transition-colors">{{ $category->name }}</a>
            </li>
            <li>
                <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                </svg>
            </li>
            <li class="font-medium text-slate-900 dark:text-white" aria-current="page">New Discussion</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="mb-8 md:mb-10">
        <h1 class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-indigo-600 dark:from-teal-400 dark:to-indigo-400 tracking-tight mb-2">
            Create a New Discussion
        </h1>
        <p class="text-lg text-slate-600 dark:text-slate-400">
            Share your thoughts, ask questions, or start a debate in <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $category->name }}</span>.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-white dark:bg-[#1a1c23] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('forum.post.store', $category->slug) }}" method="POST" id="postForm" class="p-6 md:p-8">
            @csrf
            
            <!-- Title -->
            <div class="mb-8">
                <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Discussion Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                       class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500/40 block px-4 py-3.5 shadow-sm transition-all placeholder-slate-400 dark:placeholder-slate-500 text-lg font-medium"
                       placeholder="What would you like to discuss?">
                @error('title') <p class="text-red-500 dark:text-red-400 text-sm font-medium mt-2">{{ $message }}</p> @enderror
            </div>

            <!-- Rich Text Content -->
            <div class="mb-10">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Initial Post <span class="text-red-500">*</span></label>
                <input type="hidden" name="content" id="content">
                <div class="rounded-xl shadow-sm border border-transparent overflow-hidden transition-all" id="editor-wrapper">
                    <div id="editor">{!! old('content') !!}</div>
                </div>
                @error('content') <p class="text-red-500 dark:text-red-400 text-sm font-medium mt-2">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-3 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Use the toolbar to format your text or embed images via URL.
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-5 pt-6 border-t border-slate-100 dark:border-white/5">
                <a href="{{ route('forum.category', $category->slug) }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-400 text-white px-8 py-3.5 rounded-xl font-bold shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
                    Start Discussion
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
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
            placeholder: 'Share your detailed thoughts here...',
        });

        // Add focus classes to wrapper for styling
        var editorContainer = document.querySelector('.ql-container');
        quill.on('selection-change', function(range, oldRange, source) {
            if (range) {
                editorContainer.classList.add('focus');
            } else {
                editorContainer.classList.remove('focus');
            }
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
            var value = prompt('What is the image URL? (e.g. from an image hosting service)');
            if(value) {
                this.quill.insertEmbed(range.index, 'image', value, Quill.sources.USER);
            }
        }
    });
</script>
@endpush
