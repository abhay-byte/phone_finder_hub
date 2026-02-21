@extends('layouts.app')

@section('title')
    PhoneFinderHub – Create Account
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
            <a href="{{ route('home') }}" class="inline-flex items-center mb-6 transition-transform hover:scale-105 duration-300">
                <div class="w-12 h-12 flex items-center justify-center bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                </div>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Create account</h1>
        </div>

        {{-- Card --}}
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 p-8 delay-100 animate-enter">
            <form method="POST" action="{{ route('register') }}" novalidate hx-boost="false">
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Full Name
                    </label>
                    <input
                        id="name" name="name" type="text"
                        autocomplete="name" required autofocus
                        minlength="2" maxlength="80"
                        value="{{ old('name') }}"
                        placeholder="Your name"
                        class="block w-full rounded-xl border px-4 py-3 text-sm transition-colors
                               bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                               text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                               @error('name') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Username
                        <span class="text-gray-400 dark:text-gray-500 font-normal ml-1">(3–30 chars, letters/numbers/-/_)</span>
                    </label>
                    <input
                        id="username" name="username" type="text"
                        autocomplete="username" required
                        minlength="3" maxlength="30"
                        pattern="[a-zA-Z0-9_\-]+"
                        value="{{ old('username') }}"
                        placeholder="your-handle"
                        class="block w-full rounded-xl border px-4 py-3 text-sm transition-colors
                               bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                               text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                               @error('username') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                    >
                    @error('username')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Email
                    </label>
                    <input
                        id="email" name="email" type="email"
                        autocomplete="email" required maxlength="255"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="block w-full rounded-xl border px-4 py-3 text-sm transition-colors
                               bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                               text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                               @error('email') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            id="password" name="password" type="password"
                            autocomplete="new-password" required
                            minlength="12" maxlength="1024"
                            placeholder="Min 12 chars, upper+lower+number+symbol"
                            oninput="scorePassword(this.value)"
                            class="block w-full rounded-xl border px-4 py-3 pr-12 text-sm transition-colors
                                   bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                                   text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                   focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                   @error('password') border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/10 @enderror"
                        >
                        <button type="button" onclick="togglePwd('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                aria-label="Toggle password visibility">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Password strength bar --}}
                    <div class="mt-2 h-1 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                        <div id="pwd-strength-bar" class="h-full rounded-full transition-all duration-300 w-0 bg-red-500"></div>
                    </div>
                    <p id="pwd-strength-label" class="mt-1 text-xs text-gray-400 dark:text-gray-500"> </p>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input
                            id="password_confirmation" name="password_confirmation" type="password"
                            autocomplete="new-password" required
                            maxlength="1024"
                            placeholder="Repeat password"
                            class="block w-full rounded-xl border px-4 py-3 pr-12 text-sm transition-colors
                                   bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                                   text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                   focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        >
                        <button type="button" onclick="togglePwd('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                aria-label="Toggle confirm password visibility">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] text-white font-semibold py-3 px-4 text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-lg shadow-teal-500/20">
                    Create account
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400 delay-200 animate-enter">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-teal-600 hover:text-teal-500 dark:text-teal-400 dark:hover:text-teal-300 transition-colors inline-flex items-center gap-1 hover:gap-2 duration-300">
                Sign in <span aria-hidden="true">&rarr;</span>
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function scorePassword(val) {
    const bar   = document.getElementById('pwd-strength-bar');
    const label = document.getElementById('pwd-strength-label');
    let score = 0;
    if (!val) { bar.style.width = '0'; label.textContent = ''; return; }
    if (val.length >= 12) score++;
    if (val.length >= 16) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '16%',  color: 'bg-red-500',    text: 'Very weak' },
        { pct: '32%',  color: 'bg-orange-500',  text: 'Weak' },
        { pct: '50%',  color: 'bg-yellow-500',  text: 'Fair' },
        { pct: '66%',  color: 'bg-lime-500',    text: 'Good' },
        { pct: '83%',  color: 'bg-teal-500',    text: 'Strong' },
        { pct: '100%', color: 'bg-emerald-500', text: 'Very strong' },
    ];
    const lvl = levels[Math.min(score - 1, 5)] || levels[0];
    bar.style.width = lvl.pct;
    bar.className = `h-full rounded-full transition-all duration-300 ${lvl.color}`;
    label.textContent = lvl.text;
    label.className = `mt-1 text-xs ${lvl.color.replace('bg-', 'text-')}`;
}
</script>
@endpush
@endsection
