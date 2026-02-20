@extends('layouts.app')

@section('content')
<div x-data="{ sidebarOpen: false }" @open-admin-sidebar.window="sidebarOpen = true" class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col md:flex-row">
    
    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-900/80 dark:bg-slate-950/80 backdrop-blur-sm md:hidden" 
         style="display: none;"></div>

    <!-- Mobile Drawer (Visible only on small screens) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white/95 dark:bg-slate-900/95 border-r border-slate-200 dark:border-white/5 flex flex-col py-8 px-4 space-y-2 transition-transform duration-300 ease-in-out md:hidden overflow-y-auto">
        
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="absolute top-4 right-4 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        @include('admin.partials.sidebar-content')
    </aside>

    <!-- Desktop Sidebar (Sticky on md+ screens) -->
    <aside class="hidden md:flex flex-col w-64 bg-white/95 dark:bg-slate-900/95 border-r border-slate-200 dark:border-white/5 py-8 px-4 space-y-2 sticky top-[4rem] h-[calc(100vh-4rem)] flex-shrink-0 z-10 overflow-y-auto">
        @include('admin.partials.sidebar-content')
    </aside>

    <!-- Main ContentWrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-x-hidden">
            @yield('admin-content')
        </main>
    </div>
</div>
@endsection
