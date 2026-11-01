@extends('layouts.staff')

@section('title', 'Upcoming')
@section('page-title', 'Upcoming')
@section('page-subtitle', 'Next customers in line at ' . $counter->name)
@section('active', 'queue.upcoming')

@section('content')
    {{-- Header strip --}}
    <div class="card p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
            <h2 class="text-xl font-bold text-slate-800 mt-0.5">{{ $counter->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">
                {{ $counter->location ?? '—' }} · <span class="brand-text font-semibold">{{ $counter->service?->name ?? '—' }}</span>
            </p>
        </div>
        <form method="POST" action="{{ route('staff.tokens.call-next') }}">
            @csrf
            <button type="submit" class="btn btn-success !px-5 !py-3 !text-base">▶ Call Next</button>
        </form>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h3 class="font-semibold text-slate-800">Next up</h3>
        <span class="badge b-waiting">{{ $upcoming->count() }} waiting</span>
    </div>

    @forelse ($upcoming as $index => $token)
        <div class="card p-4 sm:p-5 mb-3 flex items-center gap-4 sm:gap-6">
            <div class="shrink-0 w-12 h-12 rounded-full brand-soft brand-text grid place-items-center text-lg font-bold">
                {{ $index + 1 }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-3xl sm:text-4xl font-bold text-slate-800 truncate">{{ $token->token_no }}</div>
                <div class="text-sm text-slate-500 mt-0.5 truncate">{{ $token->user?->name ?? 'Walk-in customer' }}</div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-[10px] uppercase tracking-wider text-slate-400">Issued</div>
                <div class="text-sm font-semibold text-slate-700">{{ optional($token->issued_at)->format('H:i') }}</div>
            </div>
        </div>
    @empty
        <div class="card p-12 text-center">
            <div class="text-5xl font-bold text-slate-300">✓</div>
            <h4 class="text-lg font-semibold text-slate-700 mt-3">Queue is clear — no one waiting.</h4>
            <p class="text-sm text-slate-400 mt-1">New customers will show up here as they take a token.</p>
        </div>
    @endforelse
@endsection
