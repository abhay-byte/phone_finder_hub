@extends('layouts.app')

@section('content')
    <div
        class="bg-gray-50 dark:bg-[#0a0a0a] min-h-screen pt-14 pb-12 font-sans text-gray-900 dark:text-gray-100 selection:bg-teal-500 selection:text-white animate-fadeInUp">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav
                class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-8 transition-colors hover:text-teal-600 dark:hover:text-teal-400">
                <a href="{{ route('home') }}" class="flex items-center gap-1 group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Phones
                </a>
            </nav>

            {!! $phoneDetailsHtml !!}

                    <!-- Comments Section -->
                    <div class="mt-12">
                        @include('partials.comments', ['phone' => $phone])
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
