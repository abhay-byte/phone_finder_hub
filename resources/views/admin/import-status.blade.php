@extends('admin.layout')

@push('title')
PhoneFinderHub – Import Status
@endpush


@section('admin-content')

<div class="mb-8">
    <a href="{{ route('admin.phones.add') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-white transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Add Phone
    </a>
    <h1 class="text-2xl font-bold text-white">Import Progress</h1>
    <p class="text-slate-400 text-sm mt-1">Importing phone data. This can take 60–120 seconds.</p>
</div>

{{-- Progress Card --}}
<div class="bg-slate-900/60 rounded-2xl border border-white/5 p-8 max-w-2xl">
    {{-- Overall Progress Bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-white" id="statusLabel">Starting import…</span>
            <span class="text-xs text-slate-500" id="progressText">0 / 8</span>
        </div>
        <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
            <div id="progressBar"
                 class="h-2.5 bg-gradient-to-r from-teal-500 to-teal-400 rounded-full transition-all duration-700 ease-out"
                 style="width: 0%"></div>
        </div>
    </div>

    {{-- Steps List --}}
    <div id="stepsList" class="space-y-2.5">
        @php
        $stepDefs = [
            1 => 'Calling Python aggregator',
            2 => 'Applying manual overrides',
            3 => 'Saving phone to database',
            4 => 'Saving specification records',
            5 => 'Saving benchmark records',
            6 => 'Calculating scores',
            7 => 'Clearing caches',
            8 => 'Import complete!',
        ];
        @endphp
        @foreach($stepDefs as $num => $label)
        <div id="step-{{ $num }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-800/40 border border-white/5 transition-all">
            <div id="step-{{ $num }}-icon" class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-700/60 border border-white/5">
                <span class="text-xs font-bold text-slate-500">{{ $num }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p id="step-{{ $num }}-label" class="text-sm text-slate-400">{{ $label }}</p>
                <p id="step-{{ $num }}-msg" class="text-xs text-slate-600 hidden mt-0.5"></p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Success / Error State --}}
    <div id="resultPanel" class="hidden mt-6">
        {{-- Dynamically injected --}}
    </div>
</div>

<script>
const jobId       = @json($jobId);
const pollUrl     = @json(route('admin.phones.status.json', ['jobId' => $jobId]));
const phoneRouteBase = @json(url('/phones'));

let pollTimer = null;
let done      = false;

function iconHtml(state) {
    if (state === 'running') return `<svg class="w-4 h-4 text-teal-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
    if (state === 'done')    return `<svg class="w-4 h-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
    if (state === 'error')   return `<svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`;
    return `<span class="text-xs font-bold text-slate-500">${arguments[1]}</span>`;
}

function iconClasses(state) {
    if (state === 'running') return 'bg-teal-500/15 border-teal-500/30';
    if (state === 'done')    return 'bg-teal-500/15 border-teal-500/30';
    if (state === 'error')   return 'bg-red-500/15 border-red-500/30';
    return 'bg-slate-700/60 border-white/5';
}

function rowClasses(state) {
    if (state === 'running') return 'bg-teal-500/5 border-teal-500/20';
    if (state === 'done')    return 'bg-slate-800/40 border-white/5';
    if (state === 'error')   return 'bg-red-500/5 border-red-500/20';
    return 'bg-slate-800/40 border-white/5';
}

async function poll() {
    try {
        const res  = await fetch(pollUrl);
        const data = await res.json();

        const step    = data.step || 0;
        const total   = data.total || 8;
        const status  = data.status || 'pending';
        const steps   = data.steps  || [];

        // Update progress bar
        const pct = Math.round((step / total) * 100);
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('progressText').textContent = `${step} / ${total}`;

        // Update step rows
        steps.forEach(s => {
            const row      = document.getElementById(`step-${s.step}`);
            const iconEl   = document.getElementById(`step-${s.step}-icon`);
            const labelEl  = document.getElementById(`step-${s.step}-label`);
            const msgEl    = document.getElementById(`step-${s.step}-msg`);
            if (!row) return;

            row.className    = `flex items-center gap-3 px-4 py-3 rounded-xl border transition-all ${rowClasses(s.state)}`;
            iconEl.className = `w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 ${iconClasses(s.state)}`;
            iconEl.innerHTML = iconHtml(s.state, s.step);
            labelEl.className = `text-sm ${s.state === 'done' ? 'text-white' : s.state === 'error' ? 'text-red-300' : 'text-teal-300'}`;
            labelEl.textContent = s.name;
            if (s.msg) {
                msgEl.textContent  = s.msg;
                msgEl.className    = `text-xs mt-0.5 ${s.state === 'error' ? 'text-red-400' : 'text-slate-400'}`;
                msgEl.classList.remove('hidden');
            }
        });

        // Status label
        if (status === 'running') {
            const lastStep = steps[steps.length - 1];
            document.getElementById('statusLabel').textContent = lastStep ? lastStep.name + '…' : 'Working…';
        } else if (status === 'pending') {
            document.getElementById('statusLabel').textContent = 'Starting import…';
        }

        // Terminal states
        if (status === 'done') {
            clearInterval(pollTimer);
            done = true;
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('statusLabel').textContent = '✓ Import complete!';

            const resultPanel = document.getElementById('resultPanel');
            resultPanel.classList.remove('hidden');
            resultPanel.innerHTML = `
                <div class="bg-teal-500/10 border border-teal-500/30 rounded-xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-white">${data.phone_name || 'Phone'} added successfully!</p>
                        <div class="flex gap-3 mt-3">
                            ${data.phone_id ? `<a href="${phoneRouteBase}/${data.phone_id}" target="_blank" class="inline-flex items-center gap-1.5 text-sm bg-teal-500 hover:bg-teal-400 text-white font-semibold px-4 py-2 rounded-lg transition-all">View Phone →</a>` : ''}
                            <a href="/admin/phones/add" class="inline-flex items-center gap-1.5 text-sm bg-slate-700 hover:bg-slate-600 text-white font-medium px-4 py-2 rounded-lg transition-all">Add Another</a>
                        </div>
                    </div>
                </div>`;
        }

        if (status === 'error') {
            clearInterval(pollTimer);
            done = true;
            document.getElementById('statusLabel').textContent = '✗ Import failed';
            document.getElementById('progressBar').classList.add('bg-red-500');
            document.getElementById('progressBar').classList.remove('from-teal-500', 'to-teal-400');

            const resultPanel = document.getElementById('resultPanel');
            resultPanel.classList.remove('hidden');
            resultPanel.innerHTML = `
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-5">
                    <p class="font-semibold text-red-300 mb-1">Import failed</p>
                    <p class="text-sm text-red-400/80">${data.error || 'An unknown error occurred.'}</p>
                    <a href="/admin/phones/add" class="inline-flex items-center gap-1.5 text-sm bg-slate-700 hover:bg-slate-600 text-white font-medium px-4 py-2 rounded-lg transition-all mt-3">Try Again</a>
                </div>`;
        }

    } catch (e) {
        console.error('Poll error:', e);
    }
}

// Start polling immediately and then every 3 seconds
poll();
pollTimer = setInterval(poll, 3000);
</script>

@endsection
