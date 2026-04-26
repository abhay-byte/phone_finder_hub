@extends('layouts.app')

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-white/5">
                <div class="p-6 text-gray-900 dark:text-white">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
@endsection
