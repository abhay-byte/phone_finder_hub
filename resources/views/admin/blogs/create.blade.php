@extends('admin.layout')

@section('title', 'Write Blog')



@section('admin-content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Write Blog Post</h1>
        <p class="text-gray-500 dark:text-gray-400">Create a new tech news update or detailed phone review using Markdown.</p>
    </div>
    <a href="{{ route('admin.blogs.index') }}" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Blogs
    </a>
</div>

<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm p-6 md:p-8">
    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}"
                           class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm"
                           placeholder="Enter blog title here...">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Markdown Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" id="editor">{{ old('content') }}</textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Publishing toggle -->
                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5 border border-gray-100 dark:border-white/10">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Publishing</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published') ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publish immediately</span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">If left unchecked, this blog will be saved as a draft.</p>
                </div>

                <!-- Featured Image -->
                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5 border border-gray-100 dark:border-white/10">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Featured Cover</h3>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-white/20 border-dashed rounded-xl relative group overflow-hidden bg-white/50 dark:bg-black/20" id="imageArea">
                        <img id="imagePreview" src="" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview">
                        <div class="space-y-1 text-center relative z-10 p-2 bg-white/80 dark:bg-black/50 backdrop-blur-sm rounded-lg" id="imagePrompt">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <label for="featured_image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-teal-600 dark:text-teal-400 hover:text-teal-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>
                        </div>
                    </div>
                    @error('featured_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt (Optional)</label>
                    <textarea name="excerpt" id="excerpt" rows="3" 
                              class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm text-sm"
                              placeholder="Brief summary used in the side cards..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate from content.</p>
                </div>
                
                <hr class="border-gray-200 dark:border-white/10">

                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-teal-500/20 transition-all flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Save Blog Post
                </button>
            </div>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<!-- FontAwesome is required by EasyMDE for its toolbar icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Tailwind/EasyMDE adjustments for Dark Mode */
    .editor-toolbar {
        border-color: #e5e7eb;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        background-color: #f9fafb;
    }
    .dark .editor-toolbar {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: rgba(255, 255, 255, 0.05);
    }
    .dark .editor-toolbar > button {
        color: #9ca3af;
    }
    .dark .editor-toolbar > button:hover, .dark .editor-toolbar > button.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.1);
        color: white;
    }
    .dark .editor-toolbar > i.separator {
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        border-left: 1px solid rgba(255, 255, 255, 0.1);
    }
    .CodeMirror {
        border-color: #e5e7eb;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.875rem;
        min-height: 400px;
        color: #1f2937;
    }
    .dark .CodeMirror {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: transparent;
        color: #d1d5db;
    }
    .dark .CodeMirror-cursor {
        border-left-color: white;
    }
    .dark .editor-preview {
        background-color: #121212;
        color: white;
    }
</style>
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('imagePrompt').classList.add('bg-white/80', 'dark:bg-black/50', 'backdrop-blur-sm');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Wrap initialization in a function so it can be called repeatedly via HTMX or directly
    (function initEasyMDE() {
        const editorElement = document.getElementById('editor');
        if(!editorElement) return;

        // If an editor instance already exists on this element, HTMX may be re-swapping it, so don't re-init blindly
        if (editorElement.nextElementSibling && editorElement.nextElementSibling.classList.contains('EasyMDEContainer')) {
            return;
        }

        const easyMDE = new EasyMDE({
            element: editorElement,
            spellChecker: false,
            autosave: {
                enabled: true,
                uniqueId: "blog_create_autosave",
                delay: 1000,
            },
            uploadImage: true,
            imageUploadFunction: function(file, onSuccess, onError) {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('admin.blogs.upload-image') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Upload failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.url) {
                        onSuccess(data.url);
                    } else {
                        onError('Invalid response from server');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    onError(error.message || 'Image upload failed');
                });
            },
            toolbar: [
                "bold", "italic", "heading", "|", 
                "quote", "unordered-list", "ordered-list", "|", 
                "link", "image", "|", 
                "preview", "side-by-side", "fullscreen", "|", 
                "guide"
            ],
            placeholder: "Compose an epic in Markdown...",
        });
    })();
</script>
@endsection
