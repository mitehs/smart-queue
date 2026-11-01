@extends('layouts.user')

@section('title', 'Choose Place')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        {{-- Stepper --}}
        <div class="flex items-center gap-2 mb-8">
            <div class="step step-active">
                <span class="step-dot">1</span>
                <span class="text-sm font-semibold hidden sm:inline">Place</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <span class="step-dot">2</span>
                <span class="text-sm font-medium text-slate-500 hidden sm:inline">Service</span>
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

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Where are you going?</h1>
            <p class="text-sm text-slate-500">Choose the place you want to visit to see its services.</p>
        </div>

        @if ($places->isEmpty())
            <div class="card p-12 text-center">
                <div class="text-3xl mb-2">🏢</div>
                <div class="font-semibold text-slate-700">No places available</div>
                <p class="text-sm text-slate-500 mt-1">There are no locations registered right now. Please check back later.</p>
            </div>
        @else
            @php $icons = ['bank' => '🏦', 'government' => '🏛️', 'hospital' => '🏥', 'office' => '🏢', 'service' => '🔧']; @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($places as $place)
                    <div class="card p-5 flex flex-col hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <span class="text-3xl">{{ $icons[$place->type] ?? '🏢' }}</span>
                            <span class="badge brand-soft brand-text uppercase">{{ ucfirst($place->type) }}</span>
                        </div>
                        <h3 class="mt-3 font-semibold text-slate-800">{{ $place->name }}</h3>
                        @if ($place->address)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $place->address }}</p>
                        @endif
                        <div class="mt-2 text-sm text-slate-500 flex-1">
                            {{ $place->services_count }} service{{ $place->services_count === 1 ? '' : 's' }} available
                        </div>
                        <a href="{{ route('queue.service', ['place_id' => $place->id]) }}" class="btn btn-brand mt-4 w-full">Choose</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
