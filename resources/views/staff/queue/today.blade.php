@extends('layouts.staff')

@section('title', "Today's Tokens")
@section('page-title', "Today's Tokens")
@section('page-subtitle', 'Every token issued at ' . $counter->name . ' today')
@section('active', 'queue.today')

@section('content')
    {{-- KPI row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="kpi border-l-4 !border-l-amber-400">
            <div class="n text-amber-600">{{ $stats['waiting'] }}</div>
            <div class="t">Waiting</div>
        </div>
        <div class="kpi border-l-4 !border-l-emerald-500">
            <div class="n text-emerald-600">{{ $stats['done'] }}</div>
            <div class="t">Completed</div>
        </div>
        <div class="kpi border-l-4 !border-l-red-400">
            <div class="n text-red-500">{{ $stats['missed'] }}</div>
            <div class="t">Missed</div>
        </div>
        <div class="kpi border-l-4 !border-l-slate-300">
            <div class="n">{{ $stats['total'] }}</div>
            <div class="t">Total Today</div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-slate-800">Tokens · {{ $counter->name }}</h3>
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
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Called</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td class="font-bold">{{ $token->token_no }}</td>
                            <td>{{ $token->user?->name ?? '—' }}</td>
                            <td>{{ $token->user?->phone ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ optional($token->called_at)->format('H:i') ?? '—' }}</td>
                            <td>{{ optional($token->completed_at)->format('H:i') ?? '—' }}</td>
                            <td>
                                <a href="{{ route('staff.tokens.show', $token) }}" class="text-xs font-semibold brand-text hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-slate-400 py-6">No tokens match this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $tokens->links() }}
        </div>
    </div>
@endsection
