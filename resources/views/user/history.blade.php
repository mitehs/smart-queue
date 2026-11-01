@extends('layouts.user')

@section('title', 'History')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Your token history</h1>
                <p class="text-sm text-slate-500">{{ $tokens->total() }} token{{ $tokens->total() === 1 ? '' : 's' }} found</p>
            </div>
            <a href="{{ route('queue.place') }}" class="btn btn-brand self-start">Book a Token</a>
        </div>

        {{-- Search + filter --}}
        <form method="GET" action="{{ route('user.history') }}" class="card p-4 mb-6 flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="lbl" for="q">Search token number</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="e.g. A-42" class="inp">
            </div>
            <div class="sm:w-56">
                <label class="lbl" for="status">Status</label>
                <select id="status" name="status" class="inp">
                    <option value="">All statuses</option>
                    @foreach (['waiting', 'called', 'serving', 'done', 'skipped', 'no_show', 'cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-brand">Filter</button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('user.history') }}" class="btn btn-ghost">Reset</a>
                @endif
            </div>
        </form>

        <div class="card overflow-hidden">
            @if ($tokens->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-3xl mb-2">📭</div>
                    <div class="font-semibold text-slate-700">No tokens found</div>
                    <p class="text-sm text-slate-500 mt-1">
                        @if (request()->hasAny(['q', 'status']))
                            Nothing matches your filters. Try clearing them.
                        @else
                            You haven't booked any tokens yet.
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Date</th>
                                <th>Place</th>
                                <th>Service</th>
                                <th>Counter</th>
                                <th>Status</th>
                                <th>Issued</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tokens as $t)
                                <tr>
                                    <td class="font-bold brand-text">{{ $t->token_no }}</td>
                                    <td>{{ $t->date }}</td>
                                    <td>{{ $t->place?->name ?? '—' }}</td>
                                    <td>{{ $t->service?->name ?? '—' }}</td>
                                    <td>{{ $t->counter?->name ?? '—' }}</td>
                                    <td><span class="badge b-{{ $t->status }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
                                    <td>{{ $t->issued_at?->format('H:i') ?? '—' }}</td>
                                    <td>{{ $t->completed_at?->format('H:i') ?? '—' }}</td>
                                    <td>
                                        <div class="flex items-center gap-3 text-xs font-semibold">
                                            <a href="{{ route('queue.ticket', $t) }}" class="brand-text hover:underline">View</a>
                                            <a href="{{ route('queue.progress', $t) }}" class="brand-text hover:underline">Progress</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4">
                    {{ $tokens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
