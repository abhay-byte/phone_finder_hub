@extends('layouts.app')

@section('title', 'Tech News & Reviews - Phone Finder Hub')
@section('description', 'Read the latest deep-dive phone reviews, tech updates, and industry news from our authors.')

@section('content')
<main class="w-full bg-[#f8f9fa] dark:bg-black min-h-screen">
    <!-- Hero Header -->
    <div class="bg-white dark:bg-[#121212] border-b border-gray-200 dark:border-white/10 pt-24 pb-12 px-4 shadow-sm relative overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-teal-500/10 dark:bg-teal-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 dark:bg-purple-500/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 dark:bg-teal-500/10 border border-teal-100 dark:border-teal-500/20 text-teal-700 dark:text-teal-400 text-xs font-bold uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                Editorials
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Tech News & Reviews</h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto font-medium">Dive deep into our latest smartphone evaluations, industry news, and tech opinions curated by our expert authors.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {!! $blogsHtml !!}
        </div>

        @if($blogs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
