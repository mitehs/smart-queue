@extends('layouts.user')

@section('title', 'Live Tracking')

@section('content')
    @php
        $order = ['waiting' => 1, 'called' => 2, 'serving' => 3, 'done' => 4];
        $pos = $order[$token->status] ?? 1;
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6"
         id="live-token"
         data-token="{{ $token->token_no }}"
         data-status="{{ $token->status }}"
         data-counter="{{ $token->counter?->name ?? '' }}">

        {{-- Turn banner --}}
        @if (in_array($token->status, ['called', 'serving']))
            <div class="rounded-xl bg-emerald-600 text-white px-5 py-4 mb-6 flex items-center gap-3 shadow-lg shadow-emerald-200">
                <span class="text-2xl">🎉</span>
                <div>
                    <div class="font-bold">It's your turn!</div>
                    <div class="text-sm text-emerald-100">Please go to {{ $token->counter?->name ?? 'the counter' }} now.</div>
                </div>
            </div>
        @endif

        {{-- Big token card --}}
        <div class="card overflow-hidden mb-6">
            <div class="brand-bg text-white px-5 py-3 flex items-center justify-between">
                <span class="font-semibold text-sm uppercase tracking-wider">Live Token</span>
                <span class="badge b-{{ $token->status }}">{{ ucfirst(str_replace('_', ' ', $token->status)) }}</span>
            </div>
            <div class="p-6 text-center">
                <div class="text-xs uppercase tracking-widest text-slate-400">Your token</div>
                <div class="text-6xl font-extrabold brand-text mt-1">{{ $token->token_no }}</div>

                <div class="mt-5 grid grid-cols-3 gap-4 text-left">
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">Service</div>
                        <div class="font-semibold text-slate-800">{{ $token->service?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
                        <div class="font-semibold text-slate-800">{{ $token->counter?->name ?? 'Not assigned' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400">Place</div>
                        <div class="font-semibold text-slate-800">{{ $token->place?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Progress stepper --}}
            <div class="px-6 pb-6">
                <div class="flex items-center gap-2">
                    <div class="step {{ $pos >= 1 ? 'step-done' : '' }} {{ $pos === 1 ? 'step-active' : '' }}">
                        <span class="step-dot">✓</span>
                        <span class="text-xs font-semibold hidden sm:inline">Booked</span>
                    </div>
                    <div class="step-line {{ $pos >= 2 ? 'line-done' : '' }}"></div>
                    <div class="step {{ $pos >= 2 ? 'step-done' : '' }} {{ $pos === 2 ? 'step-active' : '' }}">
                        <span class="step-dot">{{ $pos >= 2 ? '✓' : '2' }}</span>
                        <span class="text-xs font-semibold hidden sm:inline">Waiting</span>
                    </div>
                    <div class="step-line {{ $pos >= 3 ? 'line-done' : '' }}"></div>
                    <div class="step {{ $pos >= 3 ? 'step-done' : '' }} {{ $pos === 3 ? 'step-active' : '' }}">
                        <span class="step-dot">{{ $pos >= 3 ? '✓' : '3' }}</span>
                        <span class="text-xs font-semibold hidden sm:inline">Called</span>
                    </div>
                    <div class="step-line {{ $pos >= 4 ? 'line-done' : '' }}"></div>
                    <div class="step {{ $pos >= 4 ? 'step-done' : '' }} {{ $pos === 4 ? 'step-active' : '' }}">
                        <span class="step-dot">{{ $pos >= 4 ? '✓' : '4' }}</span>
                        <span class="text-xs font-semibold hidden sm:inline">Served</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wait info --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="card p-5 text-center">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">People ahead of you</div>
                <div class="text-3xl font-bold text-slate-800 mt-1">{{ $peopleAhead }}</div>
            </div>
            <div class="card p-5 text-center">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Estimated wait</div>
                <div class="text-3xl font-bold text-slate-800 mt-1">{{ $peopleAhead * ($token->service?->est_minutes ?? 0) }} <span class="text-base font-semibold text-slate-500">min</span></div>
            </div>
        </div>

        {{-- Note --}}
        <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm flex items-start gap-2 mb-6">
            <span>🔄</span>
            <span>This page refreshes automatically every few seconds. Listen for the announcement at the venue as well.</span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('queue.ticket', $token) }}" class="btn btn-outline">View Ticket</a>
            <a href="{{ route('queue.progress', $token) }}" class="btn btn-ghost">View Progress</a>
            @if ($token->status === 'waiting')
                <a href="{{ route('queue.cancel', $token) }}"
                   class="btn btn-ghost text-red-600 hover:bg-red-50"
                   onclick="return confirm('Are you sure you want to cancel this token? This cannot be undone.')">Cancel Token</a>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const wrap = document.getElementById('live-token');
            if (!wrap) return;

            setInterval(function () {
                fetch('{{ route('queue.status') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data || !data.ok) return;
                    if (data.token_no !== wrap.dataset.token) return;

                    const changed = data.status !== wrap.dataset.status
                        || (data.counter || '') !== (wrap.dataset.counter || '');

                    if (changed) {
                        location.reload();
                    }
                })
                .catch(function () {});
            }, 4000);
        })();
    </script>
@endpush
