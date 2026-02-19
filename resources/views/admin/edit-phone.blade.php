@extends('admin.layout')

@push('title')
Edit Phone: {{ $phone->name }}
@endpush

@section('admin-content')

@php
    function renderInput($label, $name, $value, $type = 'text', $required = false) {
        $reqAttr = $required ? 'required' : '';
        $stepAttr = $type === 'number' ? 'step="any"' : '';
        $val = e($value);
        return <<<HTML
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">$label</label>
            <input type="$type" name="$name" value="$val" $reqAttr $stepAttr
                   class="w-full bg-slate-800/60 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/40 focus:border-teal-500/40 transition-all">
        </div>
HTML;
    }
    
    function renderCheckbox($label, $name, $checked, $value = '1') {
        $isChecked = $checked ? 'checked' : '';
        // Hidden input ensures a value is sent even if unchecked (defaulting to 0 or empty string)
        $hiddenVal = ($value === 'Yes') ? '' : '0'; 
        return <<<HTML
        <label class="flex items-center gap-3 cursor-pointer group select-none">
            <input type="hidden" name="$name" value="$hiddenVal">
            <input type="checkbox" name="$name" value="$value" $isChecked
                   class="w-5 h-5 rounded border-white/10 bg-slate-800/60 text-teal-500 focus:ring-teal-500/40 transition-all">
            <span class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">$label</span>
        </label>
HTML;
    }
@endphp

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-white">Edit Phone</h1>
        <p class="text-slate-400 text-sm mt-1">Editing <span class="text-teal-400">{{ $phone->name }}</span> (ID: {{ $phone->id }})</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('phones.show', $phone) }}" target="_blank" class="text-slate-400 hover:text-white text-sm transition-colors flex items-center gap-1">
            View Live Page <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
        <button type="submit" form="editForm" class="bg-teal-500 hover:bg-teal-400 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/40 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Save Changes
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-500/10 border border-green-500/30 rounded-2xl p-4 flex items-center gap-3 animate-fade-in-up">
    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div>
        <h3 class="text-green-400 font-bold text-sm">Success</h3>
        <p class="text-green-300 text-sm">{{ session('success') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-2xl p-5">
    <p class="text-sm font-semibold text-red-400 mb-2">Please fix errors:</p>
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
        <li class="text-sm text-red-300">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.phones.update', $phone) }}" method="POST" id="editForm" x-data="{ tab: 'identity' }">
    @csrf
    @method('PUT')

    {{-- Tabs Navigation --}}
    <div class="flex flex-wrap gap-1 mb-6 border-b border-white/10">
        @foreach(['identity' => 'Identity', 'body' => 'Body & Display', 'platform' => 'Platform', 'camera' => 'Cameras', 'connectivity' => 'Connectivity', 'battery' => 'Battery', 'benchmarks' => 'Benchmarks'] as $key => $label)
        <button type="button" @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'text-teal-400 border-teal-400 bg-white/[0.03]' : 'text-slate-400 hover:text-slate-200 border-transparent hover:bg-white/[0.02]'"
                class="px-5 py-3 text-sm font-semibold transition-all border-b-2 rounded-t-lg -mb-[2px]">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── Identity Tab ──────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'identity'" class="space-y-5 animate-fade-in">
        <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6">
            <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-4">
                <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center text-teal-400 font-bold">1</div>
                <h3 class="text-lg font-bold text-white">Core Identity</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {!! renderInput('Phone Name', 'name', $phone->name, 'text', true) !!}
                {!! renderInput('Brand', 'brand', $phone->brand, 'text', true) !!}
                {!! renderInput('Model Variant', 'model_variant', $phone->model_variant) !!}
                {!! renderInput('Release Date', 'release_date', $phone->release_date?->format('Y-m-d'), 'date') !!}
                {!! renderInput('Announced Date', 'announced_date', $phone->announced_date?->format('Y-m-d'), 'date') !!}
            </div>
        </div>

        <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6">
            <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-4">
                <div class="w-8 h-8 rounded-lg bg-violet-500/20 flex items-center justify-center text-violet-400 font-bold">₹</div>
                <h3 class="text-lg font-bold text-white">Pricing & Images</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {!! renderInput('Price (₹)', 'price', $phone->price, 'number') !!}
                {!! renderInput('Image URL', 'image_url', $phone->image_url) !!}
                {!! renderInput('Amazon URL', 'amazon_url', $phone->amazon_url) !!}
                {!! renderInput('Flipkart URL', 'flipkart_url', $phone->flipkart_url) !!}
                {!! renderInput('Amazon Price (₹)', 'amazon_price', $phone->amazon_price, 'number') !!}
                {!! renderInput('Flipkart Price (₹)', 'flipkart_price', $phone->flipkart_price, 'number') !!}
            </div>
        </div>
    </div>

    {{-- ── Body Tab ────────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'body'" class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 animate-fade-in">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @php
            $body = $phone->body ?? new \App\Models\SpecBody();
            $fields = [
                'Dimensions'=>'dimensions', 'Weight'=>'weight', 'Build Material'=>'build_material',
                'SIM'=>'sim', 'IP Rating'=>'ip_rating', 'Colors'=>'colors', 'Cooling Type'=>'cooling_type',
                'Display Type'=>'display_type', 'Display Size'=>'display_size', 'Resolution'=>'display_resolution',
                'Protection'=>'display_protection', 'Display Features'=>'display_features', 'Brightness'=>'display_brightness',
                'Measured Brightness'=>'measured_display_brightness', 'PWM Dimming'=>'pwm_dimming',
                'Screen-to-body'=>'screen_to_body_ratio', 'Pixel Density'=>'pixel_density', 'Touch Sampling'=>'touch_sampling_rate',
                'Screen Glass'=>'screen_glass', 'Screen Area'=>'screen_area', 'Aspect Ratio'=>'aspect_ratio',
                'Glass Level (1-7)'=>['glass_protection_level', 'number']
            ];
        @endphp
        @foreach($fields as $lbl => $spec)
            @php $col = is_array($spec)?$spec[0]:$spec; $typ = is_array($spec)?$spec[1]:'text'; @endphp
            {!! renderInput($lbl, "body[$col]", $body->$col, $typ) !!}
        @endforeach
        </div>
    </div>

    {{-- ── Platform Tab ────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'platform'" class="space-y-5 animate-fade-in">
        @php $plat = $phone->platform ?? new \App\Models\SpecPlatform(); @endphp
        <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6">
            <h3 class="text-white font-bold mb-4">Hardware & OS</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach([
                'OS'=>'os', 'OS Details'=>'os_details', 'Chipset'=>'chipset', 'CPU'=>'cpu', 'GPU'=>'gpu',
                'Memory Card'=>'memory_card_slot', 'Internal Storage'=>'internal_storage', 'RAM'=>'ram',
                'Storage Type'=>'storage_type', 'Turnip Level'=>'turnip_support_level', 'GPU Tier'=>'gpu_emulation_tier',
                'Custom ROM Support'=>'custom_rom_support',
                'AOSP Aesthetics (1-10)'=>['aosp_aesthetics_score', 'number'],
                'OS Openness (1-5)'=>['os_openness', 'number'],
                'RAM Min (GB)'=>['ram_min', 'number'], 'RAM Max (GB)'=>['ram_max', 'number'],
                'Storage Min (GB)'=>['storage_min', 'number'], 'Storage Max (GB)'=>['storage_max', 'number']
            ] as $lbl => $spec)
                @php $col = is_array($spec)?$spec[0]:$spec; $typ = is_array($spec)?$spec[1]:'text'; @endphp
                {!! renderInput($lbl, "platform[$col]", $plat->$col, $typ) !!}
            @endforeach
            </div>
        </div>
        <div class="bg-slate-900/60 rounded-2xl border border-white/5 p-6">
            <h3 class="text-white font-bold mb-4">Features</h3>
            <div class="flex flex-wrap gap-6">
                {!! renderCheckbox('Bootloader Unlockable', 'platform[bootloader_unlockable]', $plat->bootloader_unlockable) !!}
                {!! renderCheckbox('Turnip Support', 'platform[turnip_support]', $plat->turnip_support) !!}
            </div>
        </div>
    </div>

    {{-- ── Camera Tab ──────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'camera'" class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 animate-fade-in">
        @php $cam = $phone->camera ?? new \App\Models\SpecCamera(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            {!! renderInput('Main Specs', 'camera[main_camera_specs]', $cam->main_camera_specs) !!}
            {!! renderInput('Main Features', 'camera[main_camera_features]', $cam->main_camera_features) !!}
            {!! renderInput('Main Video', 'camera[main_video_capabilities]', $cam->main_video_capabilities) !!}
            {!! renderInput('Selfie Specs', 'camera[selfie_camera_specs]', $cam->selfie_camera_specs) !!}
            {!! renderInput('Selfie Features', 'camera[selfie_camera_features]', $cam->selfie_camera_features) !!}
            {!! renderInput('Selfie Video', 'camera[selfie_video_capabilities]', $cam->selfie_video_capabilities) !!}
            
            <div class="col-span-1 md:col-span-2 border-t border-white/5 pt-5 mt-2">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Parsed Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {!! renderInput('Sensors', 'camera[main_camera_sensors]', $cam->main_camera_sensors) !!}
                    {!! renderInput('Apertures', 'camera[main_camera_apertures]', $cam->main_camera_apertures) !!}
                    {!! renderInput('Focal Lengths', 'camera[main_camera_focal_lengths]', $cam->main_camera_focal_lengths) !!}
                    {!! renderInput('Zoom', 'camera[main_camera_zoom]', $cam->main_camera_zoom) !!}
                    {!! renderInput('PDAF', 'camera[main_camera_pdaf]', $cam->main_camera_pdaf) !!}
                    {!! renderInput('Selfie Sensor', 'camera[selfie_camera_sensor]', $cam->selfie_camera_sensor) !!}
                    {!! renderInput('Selfie Aperture', 'camera[selfie_camera_aperture]', $cam->selfie_camera_aperture) !!}
                </div>
                <div class="flex gap-6 mt-4">
                    {!! renderCheckbox('OIS', 'camera[main_camera_ois]', $cam->main_camera_ois) !!}
                    {!! renderCheckbox('Selfie AF', 'camera[selfie_camera_autofocus]', $cam->selfie_camera_autofocus) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- ── Connectivity Tab ────────────────────────────────────────────────── --}}
    <div x-show="tab === 'connectivity'" class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 animate-fade-in">
        @php $conn = $phone->connectivity ?? new \App\Models\SpecConnectivity(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach([
                'WLAN'=>'wlan', 'Bluetooth'=>'bluetooth', 'GPS'=>'positioning', 'NFC'=>'nfc', 'Infrared'=>'infrared',
                'Radio'=>'radio', 'USB'=>'usb', 'Sensors'=>'sensors', 'Loudspeaker'=>'loudspeaker', '3.5mm Label'=>'jack_3_5mm',
                'Audio Quality'=>'audio_quality', 'Network'=>'network_bands', 'SAR'=>'sar_value'
            ] as $lbl=>$col)
            {!! renderInput($lbl, "connectivity[$col]", $conn->$col) !!}
            @endforeach
        </div>
        <div class="mt-4">
             {!! renderCheckbox('Has 3.5mm Jack', 'connectivity[has_3_5mm_jack]', $conn->has_3_5mm_jack) !!}
        </div>
    </div>

    {{-- ── Battery Tab ─────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'battery'" class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 animate-fade-in">
        @php $bat = $phone->battery ?? new \App\Models\SpecBattery(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
             {!! renderInput('Type', 'battery[battery_type]', $bat->battery_type) !!}
             {!! renderInput('Wired Charging', 'battery[charging_wired]', $bat->charging_wired) !!}
             {!! renderInput('Wireless Charging', 'battery[charging_wireless]', $bat->charging_wireless) !!}
             {!! renderInput('Reverse Charging', 'battery[charging_reverse]', $bat->charging_reverse) !!}
             {!! renderInput('Detailed Specs', 'battery[charging_specs_detailed]', $bat->charging_specs_detailed) !!}
        </div>
        <div class="mt-4 flex gap-6">
             {!! renderCheckbox('Reverse Wired', 'battery[reverse_wired]', ($bat->reverse_wired === 'Yes'), 'Yes') !!}
             {!! renderCheckbox('Reverse Wireless', 'battery[reverse_wireless]', ($bat->reverse_wireless === 'Yes'), 'Yes') !!}
        </div>
    </div>

    {{-- ── Benchmarks Tab ──────────────────────────────────────────────────── --}}
    <div x-show="tab === 'benchmarks'" class="bg-slate-900/60 rounded-2xl border border-white/5 p-6 animate-fade-in">
        @php $bench = $phone->benchmarks->first() ?? new \App\Models\Benchmark(); @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach([
                'AnTuTu v11' => 'antutu_score', 'AnTuTu v10' => 'antutu_v10_score',
                'Geekbench Single' => 'geekbench_single', 'Geekbench Multi' => 'geekbench_multi',
                '3DMark WildLife' => 'dmark_wild_life_extreme', 'Stability' => 'dmark_wild_life_stress_stability',
                'Battery Hours' => 'battery_endurance_hours', 'DXOMark' => 'dxomark_score',
                'PhoneArena Cam' => 'phonearena_camera_score', 'Free Fall' => 'free_fall_rating'
            ] as $lbl => $col)
            {!! renderInput($lbl, "benchmarks[$col]", $bench->$col, 'number') !!}
            @endforeach
            {!! renderInput('Repairability', "benchmarks[repairability_score]", $bench->repairability_score) !!}
            {!! renderInput('Energy Label', "benchmarks[energy_label]", $bench->energy_label) !!}
            {!! renderInput('Active Use Score', "benchmarks[battery_active_use_score]", $bench->battery_active_use_score) !!}
        </div>
    </div>

</form>

<style>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-fade-in-up { animation: fadeInUp 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

@endsection
