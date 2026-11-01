@extends('layouts.staff')

@section('title', 'History')
@section('page-title', 'History')
@section('page-subtitle', 'Completed, skipped and missed tokens at ' . $counter->name)
@section('active', 'queue.history')

@section('content')
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="date" class="lbl">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" class="inp !w-auto">
            </div>
            <button type="submit" class="btn btn-brand">Filter</button>
            @if (request('date'))
                <a href="{{ route('staff.queue.history') }}" class="btn btn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">
                History {{ request('date') ? '· ' . \Carbon\Carbon::parse(request('date'))->format('D, d M Y') : '' }}
            </h3>
            <span class="text-xs text-slate-400">{{ $tokens->total() }} tokens</span>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Called</th>
                        <th>Completed</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td>
                                <a href="{{ route('staff.tokens.show', $token) }}" class="font-bold brand-text">
                                    {{ $token->token_no }}
                                </a>
                            </td>
                            <td>{{ $token->user?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ optional($token->called_at)->format('H:i') ?? '—' }}</td>
                            <td>{{ optional($token->completed_at)->format('H:i') ?? '—' }}</td>
                            <td>
                                {{ $token->called_at && $token->completed_at ? (int) round($token->completed_at->diffInMinutes($token->called_at)) . ' min' : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-slate-400 py-6">No history for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $tokens->links() }}
        </div>
    </div>
@endsection
