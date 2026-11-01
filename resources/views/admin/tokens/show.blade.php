@extends('layouts.admin')

@section('title', 'Token ' . $token->token_no)
@section('page-title', 'Token ' . $token->token_no)
@section('page-subtitle', 'Token details and status controls')
@section('active', 'tokens')

@section('content')
    @php
        $countersList = \App\Models\Counter::with(['service', 'staff'])->get();
        $statusOptions = [
            'waiting' => 'Waiting',
            'called' => 'In Service',
            'serving' => 'Serving',
            'done' => 'Completed',
            'skipped' => 'Skipped',
            'no_show' => 'Missed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <a href="{{ route('admin.tokens.index') }}" class="text-xs font-semibold brand-text hover:underline">← Back to tokens</a>

    {{-- Token header --}}
    <div class="card p-6 mt-3 mb-6 brand-bg text-white flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-5">
            <div class="text-5xl font-black leading-none">{{ $token->token_no }}</div>
            <div>
                <div class="text-[11px] uppercase tracking-wider text-blue-200">Token Number</div>
                <div class="text-lg font-semibold">{{ $token->user?->name ?? 'Walk-in customer' }}</div>
                <div class="text-xs text-blue-200">{{ $token->service?->name ?? '—' }} · {{ $token->place?->name ?? '—' }}</div>
            </div>
        </div>
        <span class="badge b-{{ $token->status }}">{{ $token->status }}</span>
    </div>

    {{-- Info grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Customer</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->user?->name ?? 'Walk-in' }}</div>
            <div class="text-xs text-slate-500">{{ $token->user?->email ?? '—' }}</div>
            <div class="text-xs text-slate-500">{{ $token->user?->phone ?? '—' }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Place</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->place?->name ?? '—' }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Service</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->service?->name ?? '—' }}</div>
            @if ($token->service)
                <div class="text-xs text-slate-500">≈ {{ $token->service->est_minutes }} min</div>
            @endif
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->counter?->name ?? 'Not assigned' }}</div>
            @if ($token->counter?->staff)
                <div class="text-xs text-slate-500">{{ $token->counter->staff->name }}</div>
            @endif
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Date</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->created_at->format('D, d M Y') }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">People Ahead</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $peopleAhead }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Issued At</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ optional($token->issued_at)->format('d M Y, H:i') ?? '—' }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Called At</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ optional($token->called_at)->format('d M Y, H:i') ?? '—' }}</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Completed At</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ optional($token->completed_at)->format('d M Y, H:i') ?? '—' }}</div>
        </div>
        <div class="card p-4 sm:col-span-2 lg:col-span-3">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Served By</div>
            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->servedBy?->name ?? '—' }}</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Update form --}}
        <div class="card p-5 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 mb-4">Update Token</h3>
            <form method="POST" action="{{ route('admin.tokens.update', $token) }}">
                @csrf
                @method('PATCH')
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="lbl">Status</label>
                        <select name="status" class="inp">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $token->status) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Counter</label>
                        <select name="counter_id" class="inp">
                            <option value="">— None —</option>
                            @foreach ($countersList as $counter)
                                <option value="{{ $counter->id }}" @selected(old('counter_id', $token->counter_id) == $counter->id)>
                                    {{ $counter->name }} — {{ $counter->service?->name ?? 'No service' }}{{ $counter->staff ? ' (' . $counter->staff->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('counter_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-brand">Update Token</button>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-2">Danger Zone</h3>
            <p class="text-xs text-slate-400 mb-4">Deleting removes this token permanently. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.tokens.destroy', $token) }}" onsubmit="return confirm('Delete this token permanently?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger w-full justify-center">Delete Token</button>
            </form>
            <a href="{{ route('admin.tokens.index') }}" class="btn btn-ghost w-full justify-center mt-3">← Back to tokens</a>
        </div>
    </div>
@endsection
