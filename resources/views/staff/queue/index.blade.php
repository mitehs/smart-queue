@extends('layouts.staff')

@section('title', 'Waiting List')
@section('page-title', 'Waiting List')
@section('page-subtitle', 'Customers waiting at ' . $counter->name)
@section('active', 'queue.index')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('staff.tokens.active') }}" class="btn btn-ghost !py-1.5 !text-xs">Now Serving</a>
    </div>
@endsection

@section('content')
    {{-- Counter info strip + Call Next --}}
    <div class="card p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
            <h2 class="text-xl font-bold text-slate-800 mt-0.5">{{ $counter->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">
                {{ $counter->location ?? '—' }} · <span class="brand-text font-semibold">{{ $counter->service?->name ?? '—' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <form method="POST" action="{{ route('staff.tokens.call-next') }}">
                @csrf
                <button type="submit" class="btn btn-success !px-5 !py-3 !text-base">▶ Call Next</button>
            </form>
            <a href="{{ route('staff.tokens.active') }}" class="btn btn-ghost !py-3">Open Now Serving</a>
        </div>
    </div>

    {{-- Waiting list now --}}
    <div class="card overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Waiting list now</h3>
            <span class="badge b-waiting">{{ $waiting->count() }} waiting</span>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Token</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Issued at</th>
                        <th>Waited</th>
                        <th>People ahead</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waiting as $index => $token)
                        <tr>
                            <td>
                                <span class="inline-grid place-items-center w-7 h-7 rounded-full brand-soft brand-text font-bold text-xs">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="font-bold">{{ $token->token_no }}</td>
                            <td>{{ $token->user?->name ?? '—' }}</td>
                            <td>{{ $token->user?->phone ?? '—' }}</td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ $token->issued_at ? (int) round($token->issued_at->diffInMinutes(now())) . ' min' : '—' }}</td>
                            <td>{{ $token->people_ahead ?? $index }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-slate-400 py-6">Queue is clear — no one waiting.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All today's tokens --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-slate-800">All today's tokens</h3>
            <form method="GET" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="inp !w-auto !py-1.5 !text-xs">
                    <option value="">All statuses</option>
                    @foreach (['waiting', 'called', 'serving', 'done', 'skipped', 'no_show', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($all as $token)
                        <tr>
                            <td class="font-bold">{{ $token->token_no }}</td>
                            <td>{{ $token->user?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ optional($token->completed_at)->format('H:i') ?? '—' }}</td>
                            <td>
                                <a href="{{ route('staff.tokens.show', $token) }}" class="text-xs font-semibold brand-text hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-400 py-6">No tokens match this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $all->links() }}
        </div>
    </div>
@endsection
