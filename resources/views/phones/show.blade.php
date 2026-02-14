@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Image & Value Card -->
            <div class="space-y-6">
                <!-- Image Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden p-6">
                     @if($phone->image_url)
                        <img src="{{ $phone->image_url }}" alt="{{ $phone->name }}" class="w-full h-auto object-contain rounded-lg">
                    @endif
                </div>

                <!-- Value Score Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 p-6 text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Value Score</h3>
                    <div class="mt-2 flex items-baseline justify-center gap-x-2">
                        <span class="text-5xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $phone->value_score }}</span>
                        <span class="text-sm font-semibold leading-6 tracking-wide text-gray-400">pts/₹1k</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Based on AnTuTu vs Price. Higher is better.
                    </p>
                </div>

                 <!-- Price Card -->
                <div class="bg-indigo-600 rounded-2xl shadow-sm p-6 text-center text-white">
                    <h3 class="text-indigo-200 text-sm font-medium uppercase tracking-wider">Current Price</h3>
                    <div class="mt-2 text-4xl font-bold">₹{{ number_format($phone->price) }}</div>
                    <p class="mt-4 text-indigo-100 text-sm">Release: {{ $phone->release_date ? $phone->release_date->format('M Y') : 'N/A' }}</p>
                </div>
            </div>

            <!-- Right Column: Specifications -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title Header -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $phone->name }}</h1>
                    <p class="mt-1 text-lg text-gray-500 dark:text-gray-400">{{ $phone->brand }} • {{ $phone->model_variant }}</p>
                </div>

                <!-- Benchmarks -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Performance Benchmarks</h2>
                    </div>
                    @if($phone->benchmarks)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-8 p-6">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">AnTuTu v10</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($phone->benchmarks->antutu_score) }}</dd>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 mt-2">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(($phone->benchmarks->antutu_score / 3000000) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Geekbench 6 (Multi/Single)</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ number_format($phone->benchmarks->geekbench_multi) }} <span class="text-sm text-gray-400 font-normal">/ {{ number_format($phone->benchmarks->geekbench_single) }}</span>
                            </dd>
                             <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 mt-2">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ min(($phone->benchmarks->geekbench_multi / 10000) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">3DMark Wild Life Extreme</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($phone->benchmarks->dmark_wild_life_extreme ?? 0) }}</dd>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 mt-2">
                                <div class="bg-rose-600 h-2.5 rounded-full" style="width: {{ min(($phone->benchmarks->dmark_wild_life_extreme / 8000) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Battery Endurance (Hours)</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $phone->benchmarks->battery_endurance_hours ?? 'N/A' }}h</dd>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 mt-2">
                                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ min(($phone->benchmarks->battery_endurance_hours / 24) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </dl>
                    @else
                    <div class="p-6 text-gray-500">No benchmark data available.</div>
                    @endif
                </div>

                <!-- Platform Specs -->
                 <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Platform & Memory</h2>
                    </div>
                    <div class="p-6">
                        @if($phone->platform)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Chipset</span> <span class="dark:text-white font-medium">{{ $phone->platform->chipset }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">OS</span> <span class="dark:text-white font-medium">{{ $phone->platform->os }}</span></div>
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">CPU</span> <span class="dark:text-white font-medium">{{ $phone->platform->cpu }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">GPU</span> <span class="dark:text-white font-medium">{{ $phone->platform->gpu }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">RAM</span> <span class="dark:text-white font-medium">{{ $phone->platform->ram }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Storage</span> <span class="dark:text-white font-medium">{{ $phone->platform->internal_storage }} ({{ $phone->platform->storage_type }})</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Card Slot</span> <span class="dark:text-white font-medium">{{ $phone->platform->memory_card_slot }}</span></div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Display & Body -->
                 <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Display & Body</h2>
                    </div>
                    <div class="p-6">
                        @if($phone->body)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">Build</span> <span class="dark:text-white font-medium">{{ $phone->body->build_material }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Dimensions</span> <span class="dark:text-white font-medium">{{ $phone->body->dimensions }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Weight</span> <span class="dark:text-white font-medium">{{ $phone->body->weight }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">SIM</span> <span class="dark:text-white font-medium">{{ $phone->body->sim }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">IP Rating</span> <span class="dark:text-white font-medium">{{ $phone->body->ip_rating }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Colors</span> <span class="dark:text-white font-medium">{{ $phone->body->colors }}</span></div>
                            <div class="col-span-full border-t border-gray-100 dark:border-slate-700 my-2"></div>
                             <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Type</span> <span class="dark:text-white font-medium">{{ $phone->body->display_type }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Size</span> <span class="dark:text-white font-medium">{{ $phone->body->display_size }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Resolution</span> <span class="dark:text-white font-medium">{{ $phone->body->display_resolution }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Protection</span> <span class="dark:text-white font-medium">{{ $phone->body->display_protection }}</span></div>
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">Features</span> <span class="dark:text-white font-medium">{{ $phone->body->display_features }}</span></div>
                        </div>
                        @else
                            <div class="text-gray-500">Body specifications not available.</div>
                        @endif
                    </div>
                </div>

                <!-- Camera Specs -->
                 <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Camera System</h2>
                    </div>
                    <div class="p-6">
                        @if($phone->camera)
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <h4 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2">Main Camera</h4>
                                <div class="space-y-2">
                                    <div><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Specs</span> <span class="dark:text-white block">{{ $phone->camera->main_camera_specs }}</span></div>
                                    <div><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Features</span> <span class="dark:text-white block">{{ $phone->camera->main_camera_features }}</span></div>
                                    <div><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Video</span> <span class="dark:text-white block">{{ $phone->camera->main_video_capabilities }}</span></div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                                <h4 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2">Selfie Camera</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Specs</span> <span class="dark:text-white block">{{ $phone->camera->selfie_camera_specs }}</span></div>
                                    <div><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Features</span> <span class="dark:text-white block">{{ $phone->camera->selfie_camera_features }}</span></div>
                                    <div><span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Video</span> <span class="dark:text-white block">{{ $phone->camera->selfie_video_capabilities }}</span></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Connectivity & Battery -->
                 <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Connectivity & Battery</h2>
                    </div>
                    <div class="p-6">
                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                            @if($phone->connectivity)
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">WLAN</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->wlan }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Bluetooth</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->bluetooth }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">GPS</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->positioning }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">NFC/Infrared</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->nfc }} / {{ $phone->connectivity->infrared }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">USB</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->usb }}</span></div>
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">Sensors</span> <span class="dark:text-white font-medium">{{ $phone->connectivity->sensors }}</span></div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Audio</span> <span class="dark:text-white font-medium">Jack: {{ $phone->connectivity->jack_3_5mm }} • {{ $phone->connectivity->loudspeaker }}</span></div>
                            @endif

                             @if($phone->battery)
                            <div class="sm:col-span-2 border-t border-gray-100 dark:border-slate-700 pt-4 mt-2">
                                <h4 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mb-2">Battery</h4>
                            </div>
                            <div><span class="text-gray-500 dark:text-gray-400 block text-sm">Type</span> <span class="dark:text-white font-medium">{{ $phone->battery->battery_type }}</span></div>
                            <div class="sm:col-span-2"><span class="text-gray-500 dark:text-gray-400 block text-sm">Charging</span> <span class="dark:text-white font-medium">{{ $phone->battery->charging_wired }} wired, {{ $phone->battery->charging_wireless }} wireless, {{ $phone->battery->charging_reverse }} reverse</span></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
