@extends('layouts.user')

@section('title', 'Choose Service')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        {{-- Stepper --}}
        <div class="flex items-center gap-2 mb-8">
            <div class="step step-done">
                <span class="step-dot">✓</span>
                <span class="text-sm font-semibold hidden sm:inline">Place</span>
            </div>
            <div class="step-line line-done"></div>
            <div class="step step-active">
                <span class="step-dot">2</span>
                <span class="text-sm font-semibold hidden sm:inline">Service</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <span class="step-dot">3</span>
                <span class="text-sm font-medium text-slate-500 hidden sm:inline">Counter</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <span class="step-dot">4</span>
                <span class="text-sm font-medium text-slate-500 hidden sm:inline">Token</span>
            </div>
        </div>

        {{-- Chosen place --}}
        <div class="card p-4 mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl brand-soft brand-text grid place-items-center font-bold">📍</span>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Place</div>
                    <div class="font-semibold text-slate-800">{{ $place->name }}</div>
                </div>
            </div>
            <a href="{{ route('queue.place') }}" class="btn btn-ghost !py-1.5 !text-xs">Change</a>
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">What do you need?</h1>
            <p class="text-sm text-slate-500">Pick the service you need at {{ $place->name }}.</p>
        </div>

        @if ($services->isEmpty())
            <div class="card p-12 text-center">
                <div class="text-3xl mb-2">🧾</div>
                <div class="font-semibold text-slate-700">No services available</div>
                <p class="text-sm text-slate-500 mt-1">This place has no active services right now. Try another place.</p>
                <a href="{{ route('queue.place') }}" class="btn btn-brand mt-4">Choose another place</a>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($services as $service)
                    <div class="card p-5 flex flex-col hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-slate-800">{{ $service->name }}</h3>
                            @if ($service->code)
                                <span class="badge brand-soft brand-text shrink-0">{{ $service->code }}</span>
                            @endif
                        </div>
                        @if ($service->description)
                            <p class="mt-1 text-sm text-slate-500 flex-1">{{ $service->description }}</p>
                        @else
                            <div class="flex-1"></div>
                        @endif
                        <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
                            <span class="flex items-center gap-1">⏱️ {{ $service->est_minutes }} min est.</span>
                            <span class="flex items-center gap-1">🧑‍💼 {{ $service->free_counters }} counter{{ $service->free_counters === 1 ? '' : 's' }}</span>
                        </div>
                        <a href="{{ route('queue.counter', ['place_id' => $place->id, 'service_id' => $service->id]) }}" class="btn btn-brand mt-4 w-full">Choose</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
