@extends('admin.layout')

@section('title', 'Edit Blog')



@section('admin-content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Blog Post</h1>
        <p class="text-gray-500 dark:text-gray-400">Update your content, featured image, or publishing status.</p>
    </div>
    <div class="flex items-center gap-3">
        @if($blog->is_published)
            <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/40 px-4 py-2 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                View Public Post
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        @endif
        <a href="{{ route('admin.blogs.index') }}" class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium flex items-center gap-2 px-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back
        </a>
    </div>
</div>

<div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm p-6 md:p-8">
    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" id="blogForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $blog->title) }}"
                           class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm"
                           placeholder="Enter blog title here...">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Markdown Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" id="editor">{{ old('content', $blog->content) }}</textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Publishing toggle -->
                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5 border border-gray-100 dark:border-white/10">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Publishing</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Published</span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        @if($blog->is_published && $blog->published_at)
                            Live since {{ $blog->published_at->format('M j, Y H:i') }}
                        @else
                            Currently saved as a draft.
                        @endif
                    </p>
                </div>

                <!-- Featured Image -->
                @php
                    $isUrlImage = $blog->featured_image && !str_starts_with($blog->featured_image, '/storage/');
                @endphp
                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-5 border border-gray-100 dark:border-white/10" x-data="{ imageType: '{{ $isUrlImage ? 'url' : 'upload' }}' }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Featured Cover</h3>
                    </div>

                    <!-- Image Type Toggle -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="image_type" value="upload" x-model="imageType" class="text-teal-600 focus:ring-teal-500 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Upload Image</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="image_type" value="url" x-model="imageType" class="text-teal-600 focus:ring-teal-500 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Image URL</span>
                        </label>
                    </div>

                    <!-- Upload Image Area -->
                    <div x-show="imageType === 'upload'" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-white/20 border-dashed rounded-xl relative group overflow-hidden bg-white/50 dark:bg-black/20" id="imageArea">
                        <img id="imagePreviewUpload" src="{{ !$isUrlImage ? $blog->featured_image : '' }}" class="absolute inset-0 w-full h-full object-cover {{ !$isUrlImage && $blog->featured_image ? '' : 'hidden' }}" alt="Preview">
                        <div class="space-y-1 text-center relative z-10 p-2 {{ !$isUrlImage && $blog->featured_image ? 'bg-white/80 dark:bg-black/50 backdrop-blur-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity' : '' }}" id="imagePromptUpload">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <label for="featured_image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-teal-600 dark:text-teal-400 hover:text-teal-500 focus-within:outline-none">
                                    <span>{{ !$isUrlImage && $blog->featured_image ? 'Change image' : 'Upload a file' }}</span>
                                    <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*" onchange="previewImageUpload(this)">
                                </label>
                            </div>
                        </div>
                    </div>
                    @error('featured_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <!-- Image URL Area -->
                    <div x-show="imageType === 'url'" class="space-y-4" style="display: none;">
                        <input type="url" name="featured_image_url" id="featured_image_url" value="{{ old('featured_image_url', $isUrlImage ? $blog->featured_image : '') }}"
                               class="w-full bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 text-sm"
                               placeholder="https://example.com/image.jpg" oninput="previewImageUrl(this.value)">
                        
                        <div class="mt-4 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-white/20 border-dashed rounded-xl relative overflow-hidden bg-white/50 dark:bg-black/20" id="imageUrlArea" style="min-height: 150px;">
                            <img id="imagePreviewUrl" src="{{ $isUrlImage ? $blog->featured_image : '' }}" class="absolute inset-0 w-full h-full object-cover {{ $isUrlImage && $blog->featured_image ? '' : 'hidden' }}" alt="Link Preview">
                            <span id="imageUrlPlaceholder" class="text-gray-400 text-sm relative z-10 my-auto {{ $isUrlImage && $blog->featured_image ? 'hidden' : '' }}">Image preview will appear here</span>
                        </div>
                        @error('featured_image_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt (Optional)</label>
                    <textarea name="excerpt" id="excerpt" rows="3" 
                              class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-teal-500 focus:border-teal-500 block p-3 shadow-sm text-sm"
                              placeholder="Brief summary used in the side cards...">{{ old('excerpt', $blog->excerpt) }}</textarea>
                </div>
                
                <hr class="border-gray-200 dark:border-white/10">

                <button type="button" onclick="window.blogEditorInstance.togglePreview()" class="w-full bg-slate-900 border border-transparent dark:bg-white dark:text-black dark:border-gray-300 hover:bg-slate-800 dark:hover:bg-gray-100 text-white px-5 py-3 rounded-xl font-bold transition-colors flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    Toggle Live Preview
                </button>

                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-teal-500/20 transition-all flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Save Changes
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
        min-height: 70vh;
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
    
    /* Frontend Typography matching show.blade.php for accurate preview */
    .editor-preview h1, .editor-preview h2, .editor-preview h3, .editor-preview h4 {
        color: #111827;
        font-weight: 800;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        line-height: 1.3;
        letter-spacing: -0.025em;
    }
    .dark .editor-preview h1, .dark .editor-preview h2, .dark .editor-preview h3, .dark .editor-preview h4 {
        color: #ffffff;
    }
    .editor-preview h1 { font-size: 2.25rem; margin-top: 1rem; }
    .editor-preview h2 { font-size: 1.875rem; }
    .editor-preview h3 { font-size: 1.5rem; }
    .editor-preview h4 { font-size: 1.25rem; }

    /* Horizontal Rule */
    .editor-preview hr {
        border: none;
        border-top: 1.5px solid #e5e7eb;
        margin: 2.5rem 0;
        opacity: 0.6;
    }
    .dark .editor-preview hr {
        border-top-color: rgba(255, 255, 255, 0.12);
    }

    /* Tables */
    .editor-preview table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 2rem 0;
        font-size: 0.95rem;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .dark .editor-preview table {
        border-color: rgba(255,255,255,0.1);
        box-shadow: none;
    }
    .editor-preview thead {
        background: linear-gradient(135deg, #f0fdfa, #e6fffa);
    }
    .dark .editor-preview thead {
        background: rgba(20, 184, 166, 0.15);
    }
    .editor-preview th {
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #0f766e;
        text-align: left;
        border-bottom: 2px solid #99f6e4;
    }
    .dark .editor-preview th {
        color: #2dd4bf;
        border-bottom-color: rgba(20, 184, 166, 0.3);
    }
    .editor-preview td {
        padding: 0.65rem 1rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .dark .editor-preview td {
        color: #d1d5db;
        border-bottom-color: rgba(255,255,255,0.05);
    }
    .editor-preview tbody tr:nth-child(even) {
        background-color: #f9fffe;
    }
    .dark .editor-preview tbody tr:nth-child(even) {
        background-color: rgba(20, 184, 166, 0.04);
    }
    .editor-preview tbody tr:last-child td {
        border-bottom: none;
    }
    .editor-preview tbody tr:hover {
        background-color: #f0fdfa;
    }
    .dark .editor-preview tbody tr:hover {
        background-color: rgba(20, 184, 166, 0.08);
    }
    
    .editor-preview p {
        margin-bottom: 1.25rem;
        line-height: 1.8;
        color: #4b5563;
    }
    .dark .editor-preview p { color: #d1d5db; }
    
    .editor-preview img {
        border-radius: 1rem;
        margin: 2.5rem auto;
        max-width: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .editor-preview ul, .editor-preview ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        color: #4b5563;
    }
    .dark .editor-preview ul, .dark .editor-preview ol { color: #d1d5db; }
    .editor-preview li { margin-bottom: 0.5rem; }
    .editor-preview ul { list-style-type: disc; }
    .editor-preview ol { list-style-type: decimal; }
    
    .editor-preview a {
        color: #0d9488;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 4px;
        transition: color 0.2s ease;
    }
    .dark .editor-preview a { color: #2dd4bf; }
    .editor-preview blockquote {
        border-left: 4px solid #14b8a6;
        padding-left: 1.5rem;
        font-style: italic;
        color: #6b7280;
        margin: 2rem 0;
        background: #f0fdfa;
        padding: 1.5rem;
        border-radius: 0 1rem 1rem 0;
    }
    .dark .editor-preview blockquote {
        border-left-color: #0d9488;
        color: #9ca3af;
        background: rgba(13, 148, 136, 0.1);
    }
    .editor-preview pre, .editor-preview code {
        background-color: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 0.375rem;
        font-size: 0.875em;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: #ef4444;
    }
    .dark .editor-preview pre, .dark .editor-preview code {
        background-color: rgba(255,255,255,0.1);
        color: #f87171;
    }
    .editor-preview pre {
        padding: 1.5rem;
        border-radius: 1rem;
        overflow-x: auto;
        color: #e5e7eb;
        background-color: #1f2937;
        margin-bottom: 1.5rem;
    }
    .dark .editor-preview pre {
        background-color: #111827;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .editor-preview pre code { background-color: transparent; color: inherit; padding: 0; }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function previewImageUpload(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreviewUpload').src = e.target.result;
                document.getElementById('imagePreviewUpload').classList.remove('hidden');
                
                const prompt = document.getElementById('imagePromptUpload');
                prompt.classList.add('bg-white/80', 'dark:bg-black/50', 'backdrop-blur-sm', 'opacity-0', 'group-hover:opacity-100', 'transition-opacity');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewImageUrl(url) {
        const preview = document.getElementById('imagePreviewUrl');
        const placeholder = document.getElementById('imageUrlPlaceholder');
        if (url && url.match(/^https?:\/\/.+$/i)) { // Relaxed regex slightly as images don't always end in extensions online
            preview.src = url;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            preview.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');
        }
    }

    // Initialize preview if URL is already present on load
    document.addEventListener('DOMContentLoaded', () => {
        const urlInput = document.getElementById('featured_image_url');
        if (urlInput && urlInput.value) {
            previewImageUrl(urlInput.value);
        }
    });

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
                uniqueId: "blog_edit_autosave_{{ $blog->id }}",
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
        
        // Expose instance to window for external button
        window.blogEditorInstance = easyMDE;
    })();
</script>
@endsection
