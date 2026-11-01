@extends('layouts.user')

@section('title', 'Token Booked')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-10 text-center">
        {{-- Check circle --}}
        <div class="w-20 h-20 rounded-full bg-emerald-100 mx-auto grid place-items-center">
            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 L9 17 L4 12"/>
            </svg>
        </div>

        <h1 class="mt-5 text-2xl font-extrabold text-slate-900">You're in the queue!</h1>
        <p class="text-sm text-slate-500 mt-1">Your token has been booked successfully. Here are your details.</p>

        {{-- Token number --}}
        <div class="card mt-6 p-8">
            <div class="text-xs uppercase tracking-widest text-slate-400">Your token</div>
            <div class="text-6xl sm:text-7xl font-extrabold brand-text mt-2">{{ $token->token_no }}</div>

            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-left">
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Service</div>
                    <div class="font-semibold text-slate-800">{{ $token->service?->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
                    <div class="font-semibold text-slate-800">{{ $token->counter?->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Place</div>
                    <div class="font-semibold text-slate-800">{{ $token->place?->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Issued</div>
                    <div class="font-semibold text-slate-800">{{ $token->issued_at?->format('H:i') ?? '—' }}</div>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-dashed border-slate-200 grid grid-cols-2 gap-4">
                <div class="rounded-xl brand-soft p-4">
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">People ahead of you</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ $peopleAhead }}</div>
                </div>
                <div class="rounded-xl brand-soft p-4">
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Estimated wait</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">~{{ $peopleAhead * ($token->service?->est_minutes ?? 0) }} min</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('queue.active') }}" class="btn btn-brand">Track My Token</a>
            <a href="{{ route('queue.ticket', $token) }}" class="btn btn-outline">View Ticket</a>
            <a href="{{ route('queue.place') }}" class="btn btn-ghost">Book Another</a>
        </div>

        {{-- Note --}}
        <div class="mt-6 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm text-left flex items-start gap-2">
            <span>📢</span>
            <span>Please listen for the announcement and watch your token screen. Arrive at <strong>{{ $token->counter?->name ?? 'the counter' }}</strong> as soon as your token is called.</span>
        </div>
    </div>
@endsection
