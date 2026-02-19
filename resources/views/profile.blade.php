@extends('layouts.app')

@push('title')
    {{ $user->name }} – Profile
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">
        
        {{-- Header / Breadcrumb --}}
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div class="flex items-center">
                            <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="rounded-xl bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-teal-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Content --}}
        <div class="bg-white dark:bg-gray-900 shadow rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                
                {{-- Sidebar / Info --}}
                <div class="md:col-span-1 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800 p-6 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg ring-4 ring-white dark:ring-gray-900 mb-4">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ '@' . $user->username }}</p>
                        
                        @if($user->isSuperAdmin())
                            <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300">
                                Super Admin
                            </span>
                        @else
                             <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                User
                            </span>
                        @endif

                        <div class="mt-6 w-full border-t border-gray-200 dark:border-gray-700 pt-6">
                            <dl class="space-y-4 text-left">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white truncate">{{ $user->email }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Edit Form --}}
                <div class="md:col-span-2 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Profile Settings</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your account's profile information and email address.</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                {{-- Name --}}
                                <div class="sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                    <div class="mt-1">
                                        <input type="text" name="name" id="name" autocomplete="name"
                                               value="{{ old('name', $user->name) }}"
                                               class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md px-3 py-2">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Username --}}
                                <div class="sm:col-span-3">
                                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 sm:text-sm">@</span>
                                        <input type="text" name="username" id="username" autocomplete="username"
                                               value="{{ old('username', $user->username) }}"
                                               class="focus:ring-teal-500 focus:border-teal-500 flex-1 block w-full min-w-0 rounded-none rounded-r-md sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2">
                                    </div>
                                    @error('username')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="sm:col-span-6">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                    <div class="mt-1">
                                        <input id="email" name="email" type="email" autocomplete="email"
                                               value="{{ old('email', $user->email) }}"
                                               class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md px-3 py-2">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-6 border-t border-gray-100 dark:border-gray-800 pt-6 mt-2">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Change Password</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank if you don't want to change it.</p>
                                </div>

                                {{-- New Password --}}
                                <div class="sm:col-span-3">
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                    <div class="mt-1">
                                        <input type="password" name="password" id="password" autocomplete="new-password"
                                               class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md px-3 py-2">
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="sm:col-span-3">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                    <div class="mt-1">
                                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                               class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md px-3 py-2">
                                    </div>
                                </div>
                                
                                {{-- Current Password (Required for password change) --}}
                                <div class="sm:col-span-6">
                                     <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password (Required only if changing password)</label>
                                    <div class="mt-1">
                                        <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                                               class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md px-3 py-2">
                                        @error('current_password')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 dark:border-gray-800 mt-6 flex justify-end">
                            <a href="{{ route('home') }}" class="bg-white dark:bg-gray-800 py-2 px-4 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
