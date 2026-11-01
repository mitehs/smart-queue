@extends('layouts.user')

@section('title', 'Choose Counter')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">
        {{-- Stepper --}}
        <div class="flex items-center gap-2 mb-8">
            <div class="step step-done">
                <span class="step-dot">✓</span>
                <span class="text-sm font-semibold hidden sm:inline">Place</span>
            </div>
            <div class="step-line line-done"></div>
            <div class="step step-done">
                <span class="step-dot">✓</span>
                <span class="text-sm font-semibold hidden sm:inline">Service</span>
            </div>
            <div class="step-line line-done"></div>
            <div class="step step-active">
                <span class="step-dot">3</span>
                <span class="text-sm font-semibold hidden sm:inline">Counter</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <span class="step-dot">4</span>
                <span class="text-sm font-medium text-slate-500 hidden sm:inline">Token</span>
            </div>
        </div>

        {{-- Summary --}}
        <div class="card p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">Place</div>
                        <div class="font-semibold text-slate-800">{{ $place->name }}</div>
                    </div>
                    <a href="{{ route('queue.place') }}" class="text-xs font-semibold brand-text hover:underline">Change</a>
                </div>
                <span class="text-slate-300">·</span>
                <div class="flex items-center gap-2">
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">Service</div>
                        <div class="font-semibold text-slate-800">{{ $service->name }}</div>
                    </div>
                    <a href="{{ route('queue.service', ['place_id' => $place->id]) }}" class="text-xs font-semibold brand-text hover:underline">Change</a>
                </div>
            </div>
            <div class="text-xs text-slate-500">⏱️ ~{{ $service->est_minutes }} min per token</div>
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Pick a counter</h1>
            <p class="text-sm text-slate-500">Choose the counter with the shortest wait.</p>
        </div>

        @if ($counters->isEmpty())
            <div class="card p-12 text-center">
                <div class="text-3xl mb-2">🚫</div>
                <div class="font-semibold text-slate-700">No counters available</div>
                <p class="text-sm text-slate-500 mt-1">No counters available for this service right now. Try another service or check back later.</p>
                <a href="{{ route('queue.service', ['place_id' => $place->id]) }}" class="btn btn-brand mt-4">Choose another service</a>
            </div>
        @else
            <form method="POST" action="{{ route('queue.store') }}" id="counter-form">
                @csrf
                <input type="hidden" name="place_id" value="{{ $place->id }}">
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach ($counters as $c)
                        <div>
                            <input type="radio" name="counter_id" value="{{ $c->id }}" id="counter-{{ $c->id }}" class="peer sr-only" required>
                            <label for="counter-{{ $c->id }}" class="block card p-5 cursor-pointer transition hover:border-blue-300 peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-600/20 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-600/40">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $c->name }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">📍 {{ $c->location ?? 'Main hall' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">🧑‍💼 {{ $c->staff_name ?? 'Available' }}</div>
                                    </div>
                                    <span class="w-5 h-5 rounded-full border-2 border-slate-300 shrink-0 mt-1"></span>
                                </div>
                                <div class="mt-3 flex items-center gap-4 text-sm">
                                    <span class="inline-flex items-center gap-1.5 font-semibold {{ $c->waiting_count > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                        <span class="w-2 h-2 rounded-full {{ $c->waiting_count > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                        {{ $c->waiting_count }} waiting
                                    </span>
                                    <span class="text-slate-500">~{{ $c->est_wait }} min wait</span>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                @error('counter_id')
                    <p class="text-sm text-red-600 mt-3">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex items-center justify-between gap-4">
                    <a href="{{ route('queue.service', ['place_id' => $place->id]) }}" class="btn btn-ghost">← Back</a>
                    <button type="submit" id="submit-btn" disabled class="btn btn-brand disabled:opacity-50 disabled:cursor-not-allowed">Get My Token ✓</button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('counter-form');
            const btn = document.getElementById('submit-btn');
            if (!form || !btn) return;
            form.addEventListener('change', function () {
                btn.disabled = !form.querySelector('input[name="counter_id"]:checked');
            });
        })();
    </script>
@endpush
