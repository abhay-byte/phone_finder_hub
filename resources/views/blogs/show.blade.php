@extends('layouts.app')

@section('title', $blog->title . ' - Phone Finder Hub')
@section('description', $blog->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($blog->content), 150))



@section('content')
<style>
    /* Rich Text Content Styling */
    .blog-content h2, .blog-content h3, .blog-content h4 {
        color: #111827;
        font-weight: 800;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        line-height: 1.3;
        letter-spacing: -0.025em;
    }
    .dark .blog-content h2, .dark .blog-content h3, .dark .blog-content h4 {
        color: #ffffff;
    }
    .blog-content h2 { font-size: 1.875rem; }
    .blog-content h3 { font-size: 1.5rem; }
    .blog-content h4 { font-size: 1.25rem; }
    
    .blog-content p {
        margin-bottom: 1.25rem;
        line-height: 1.8;
        color: #4b5563;
    }
    .dark .blog-content p {
        color: #d1d5db;
    }
    
    .blog-content img {
        border-radius: 1rem;
        margin: 2.5rem auto;
        max-width: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .blog-content ul, .blog-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        color: #4b5563;
    }
    .dark .blog-content ul, .dark .blog-content ol {
        color: #d1d5db;
    }
    .blog-content li { margin-bottom: 0.5rem; }
    .blog-content ul { list-style-type: disc; }
    .blog-content ol { list-style-type: decimal; }
    
    .blog-content a {
        color: #0d9488;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 4px;
        transition: color 0.2s ease;
    }
    .dark .blog-content a { color: #2dd4bf; }
    .blog-content a:hover { color: #0f766e; }
    .dark .blog-content a:hover { color: #5eead4; }
    
    .blog-content blockquote {
        border-left: 4px solid #14b8a6;
        padding-left: 1.5rem;
        font-style: italic;
        color: #6b7280;
        margin: 2rem 0;
        background: #f0fdfa;
        padding: 1.5rem;
        border-radius: 0 1rem 1rem 0;
    }
    .dark .blog-content blockquote {
        border-left-color: #0d9488;
        color: #9ca3af;
        background: rgba(13, 148, 136, 0.1);
    }
    .blog-content pre, .blog-content code {
        background-color: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 0.375rem;
        font-size: 0.875em;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: #ef4444;
    }
    .dark .blog-content pre, .dark .blog-content code {
        background-color: rgba(255,255,255,0.1);
        color: #f87171;
    }
    .blog-content pre {
        padding: 1.5rem;
        border-radius: 1rem;
        overflow-x: auto;
        color: #e5e7eb;
        background-color: #1f2937;
        margin-bottom: 1.5rem;
    }
    .dark .blog-content pre {
        background-color: #111827;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .blog-content pre code {
        background-color: transparent;
        color: inherit;
        padding: 0;
    }
    
    .blog-content iframe {
        width: 100%;
        border-radius: 1rem;
        aspect-ratio: 16 / 9;
        margin: 2.5rem 0;
    }
</style>
<main class="w-full bg-[#f8f9fa] dark:bg-black min-h-screen pt-24 pb-16">
    <!-- Article Header -->
    <div class="max-w-4xl mx-auto px-4 mb-10 text-center">
        <a href="{{ route('blogs.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors uppercase tracking-widest mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
            All Articles
        </a>
        
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white leading-[1.1] tracking-tight mb-8">
            {{ $blog->title }}
        </h1>
        
        <div class="flex items-center justify-center gap-4 text-sm font-medium border-y border-gray-200 dark:border-white/10 py-4 max-w-2xl mx-auto">
            <div class="flex items-center gap-2 text-gray-900 dark:text-white">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white shadow-sm shadow-indigo-500/20">
                    {{ substr($blog->author->name ?? 'A', 0, 1) }}
                </div>
                <span>{{ $blog->author->name ?? 'Guest Author' }}</span>
            </div>
            
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>
            
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Publish Date: {{ $blog->published_at->format('M j, Y') }}
            </div>
            
            @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->id() === $blog->user_id))
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 hidden sm:block"></span>
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="hidden sm:inline-flex items-center gap-1 text-amber-500 hover:text-amber-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Post
                </a>
            @endif
        </div>
    </div>

    <!-- Featured Image -->
    @if($blog->featured_image)
    <div class="max-w-6xl mx-auto px-4 mb-14">
        <div class="w-full aspect-[21/9] md:aspect-[3/1] rounded-[2rem] overflow-hidden shadow-2xl relative">
            <img src="{{ $blog->featured_image }}" alt="Cover Image" class="w-full h-full object-cover">
            <!-- Subtle gradient overly to make the image blend better if it lacks contrast -->
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Main Content (Reader) -->
        <article class="lg:col-span-8">
            <div class="bg-white dark:bg-[#121212] p-8 md:p-12 rounded-[2rem] border border-gray-100 dark:border-white/5 shadow-sm 
                blog-content font-serif text-lg">
                <!-- Dropcap or intro text could go here, for now just render HTML securely -->
                {!! \Illuminate\Support\Str::markdown($blog->content) !!}
            </div>
        </article>
        
        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-8">
            <!-- Share Widget -->
            <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm text-center relative overflow-hidden group">
                <!-- abstract background blur -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>
                
                <h3 class="text-xs font-bold uppercase tracking-widest text-teal-600 dark:text-teal-400 mb-6 relative z-10">Share this article</h3>
                <div class="flex justify-center gap-4 relative z-10">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-blue-400 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all transform hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!');" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-indigo-500 hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all transform hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    </button>
                </div>
            </div>

            <!-- More Latest Articles -->
            @if($latestBlogs->count() > 0)
                <div class="bg-white dark:bg-[#121212] p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span> More to Read
                    </h3>
                    <div class="space-y-6">
                        @foreach($latestBlogs as $related)
                            <a href="{{ route('blogs.show', $related->slug) }}" class="group flex gap-4 items-center">
                                @if($related->featured_image)
                                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 relative bg-gray-100 dark:bg-white/5">
                                        <img src="{{ $related->featured_image }}" alt="thumbnail" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors line-clamp-2 leading-snug">{{ $related->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1">{{ $related->published_at->format('M j, Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
</main>
@endsection
