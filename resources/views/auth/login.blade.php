@extends('layouts.app')

@section('title')
    PhoneFinderHub – Login
@endsection

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-enter { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
</style>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md animate-enter">

        {{-- Logo & Heading --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-6 transition-transform hover:scale-105 duration-300">
                <div class="w-12 h-12 flex items-center justify-center bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white">PhoneFinderHub</span>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Welcome back</h1>
        </div>

        {{-- Flash Message --}}
        @if (session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400 flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Card --}}
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 p-8 delay-100 animate-enter">
            <form method="POST" action="{{ route('login') }}" novalidate hx-boost="false">
                @csrf

                {{-- Identifier --}}
                <div class="mb-5">
                    <label for="identifier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Username or Email
                    </label>
                    <input
                        id="identifier"
                        name="identifier"
                        type="text"
                        autocomplete="username"
                        autofocus
                        required
                        maxlength="255"
                        value="{{ old('identifier') }}"
                        placeholder="abhay-byte or you@example.com"
                        class="block w-full rounded-xl border px-4 py-3 text-sm transition-colors
                               bg-gray-50 dark:bg-gray-800
                               border-gray-200 dark:border-gray-700
                               text-gray-900 dark:text-white
                               placeholder-gray-400 dark:placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                               @error('identifier') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                    >
                    @error('identifier')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            minlength="8"
                            maxlength="1024"
                            placeholder="••••••••"
                            class="block w-full rounded-xl border px-4 py-3 pr-12 text-sm transition-colors
                                   bg-gray-50 dark:bg-gray-800
                                   border-gray-200 dark:border-gray-700
                                   text-gray-900 dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                   @error('password') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                        >
                        {{-- Show/hide toggle --}}
                        <button type="button" onclick="togglePwd('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                aria-label="Toggle password visibility">
                            <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center mb-6">
                    <input id="remember" name="remember" type="checkbox" value="1"
                           class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700">
                    <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        Keep me signed in
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] text-white font-semibold py-3 px-4 text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-lg shadow-teal-500/20">
                    Sign in
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400 delay-200 animate-enter">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-teal-600 hover:text-teal-500 dark:text-teal-400 dark:hover:text-teal-300 transition-colors inline-flex items-center gap-1 hover:gap-2 duration-300">
                Create one <span aria-hidden="true">&rarr;</span>
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-icon').style.opacity = isHidden ? '0.5' : '1';
}
</script>
@endpush
@endsection
