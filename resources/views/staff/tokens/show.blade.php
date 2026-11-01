@extends('layouts.staff')

@section('title', 'Token ' . $token->token_no)
@section('page-title', 'Token ' . $token->token_no)
@section('page-subtitle', 'Customer and token details')
@section('active', 'queue.index')

@section('header-actions')
    <a href="{{ route('staff.queue.index') }}" class="btn btn-ghost !py-1.5 !text-xs">← Back to waiting list</a>
@endsection

@section('content')
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Token summary --}}
        <div class="card p-6 text-center">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Token</div>
            <div class="text-5xl font-bold brand-text mt-2">{{ $token->token_no }}</div>
            <div class="mt-3">
                <span class="badge b-{{ $token->status }}">{{ $token->status }}</span>
            </div>
            <div class="text-sm font-semibold text-slate-700 mt-4">{{ $token->user?->name ?? 'Walk-in customer' }}</div>

            @if (in_array($token->status, ['done', 'skipped', 'no_show']))
                <form method="POST" action="{{ route('staff.tokens.recall', $token) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="btn btn-warn w-full justify-center">↩ Return to waiting</button>
                </form>
            @endif

            <a href="{{ route('staff.queue.index') }}" class="btn btn-ghost w-full justify-center mt-3">Back to waiting list</a>
        </div>

        {{-- Details --}}
        <div class="card p-5 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 mb-4">Details</h3>
            <div class="grid sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Customer</div>
                    <div class="text-sm font-semibold text-slate-800 mt-1">{{ $token->user?->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Email</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->user?->email ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Phone</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->user?->phone ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Service</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->service?->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->counter?->name ?? $counter->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Status</div>
                    <div class="text-sm text-slate-600 mt-1 capitalize">{{ str_replace('_', ' ', $token->status) }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Issued at</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->issued_at?->format('D, d M Y H:i') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Called at</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->called_at?->format('D, d M Y H:i') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400">Completed at</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $token->completed_at?->format('D, d M Y H:i') ?? '—' }}</div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4">
                <div class="text-[11px] uppercase tracking-wider text-slate-400 mb-1">Notes</div>
                <p class="text-sm text-slate-400">No notes</p>
            </div>
        </div>
    </div>
@endsection
