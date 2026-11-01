@extends('layouts.user')

@section('title', 'Token Progress')

@section('content')
    @php
        $order = ['waiting' => 1, 'called' => 2, 'serving' => 3, 'done' => 4];
        $pos = $order[$token->status] ?? 1;
        $isException = in_array($token->status, ['skipped', 'no_show', 'cancelled']);
        if ($isException) {
            $pos = $token->called_at ? 3 : 1;
        }
        $steps = [
            ['label' => 'Booked', 'desc' => 'Token issued', 'ts' => $token->issued_at],
            ['label' => 'Waiting', 'desc' => 'In the queue', 'ts' => $token->issued_at],
            ['label' => 'Called', 'desc' => 'Called to the counter', 'ts' => $token->called_at],
            ['label' => 'Served', 'desc' => 'Service completed', 'ts' => $token->completed_at],
        ];
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6">
        {{-- Header --}}
        <div class="card p-5 mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="text-3xl font-extrabold brand-text">{{ $token->token_no }}</div>
                <div>
                    <span class="badge b-{{ $token->status }}">{{ ucfirst(str_replace('_', ' ', $token->status)) }}</span>
                    <div class="text-xs text-slate-500 mt-1">{{ $token->service?->name ?? '—' }} · {{ $token->counter?->name ?? 'Counter TBA' }} · {{ $token->place?->name ?? '—' }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">People ahead</div>
                <div class="text-xl font-bold text-slate-800">{{ $token->isOpen() ? $peopleAhead : '—' }}</div>
            </div>
        </div>

        @if ($isException)
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm mb-6">
                This token was marked as <strong>{{ ucfirst(str_replace('_', ' ', $token->status)) }}</strong>. Only the steps completed before that are shown below.
            </div>
        @endif

        {{-- Timeline --}}
        <div class="card p-6">
            <h3 class="font-semibold text-slate-800 mb-6">Timeline</h3>

            <div class="space-y-0">
                @foreach ($steps as $i => $step)
                    @php
                        $stepNo = $i + 1;
                        $done = $stepNo < $pos || ($stepNo === $pos && $token->status === 'done') || ($stepNo <= $pos && $stepNo < 4);
                        $active = $stepNo === $pos && $token->status !== 'done' && ! $isException;
                    @endphp
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="step-dot {{ $done ? '!bg-emerald-600 !text-white' : ($active ? '!bg-blue-700 !text-white shadow-[0_0_0_5px_rgba(29,78,216,.15)]' : '') }}">
                                {{ $done ? '✓' : $stepNo }}
                            </div>
                            @if (! $loop->last)
                                <div class="w-0.5 flex-1 min-h-[2.5rem] my-1 rounded {{ $done ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                            @endif
                        </div>
                        <div class="pb-6 {{ $loop->last ? 'pb-0' : '' }}">
                            <div class="font-semibold {{ $done ? 'text-slate-800' : ($active ? 'brand-text' : 'text-slate-400') }}">{{ $step['label'] }}</div>
                            <div class="text-sm {{ $done || $active ? 'text-slate-500' : 'text-slate-400' }}">{{ $step['desc'] }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                @if ($step['ts'])
                                    {{ $step['ts']->format('d M Y, H:i') }}
                                    <span class="text-slate-300">·</span> {{ $step['ts']->diffForHumans() }}
                                @else
                                    Pending
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('queue.active') }}" class="btn btn-brand">← Back to Live Tracking</a>
            <a href="{{ route('queue.ticket', $token) }}" class="btn btn-outline">View Ticket</a>
        </div>
    </div>
@endsection
