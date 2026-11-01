@extends('layouts.admin')

@section('title', 'All Tokens')
@section('page-title', 'All Tokens')
@section('page-subtitle', 'Search and manage every queue token')
@section('active', 'tokens')

@section('content')
    @php
        $statusOptions = [
            '' => 'All statuses',
            'waiting' => 'Waiting',
            'called' => 'In Service',
            'serving' => 'Serving',
            'done' => 'Completed',
            'skipped' => 'Skipped',
            'no_show' => 'Missed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    {{-- Filter bar --}}
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[12rem]">
                <label class="lbl">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="inp" placeholder="Token no or customer name...">
            </div>
            <div class="w-full sm:w-44">
                <label class="lbl">Place</label>
                <select name="place" class="inp">
                    <option value="">All places</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" @selected(($filters['place'] ?? '') == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="lbl">Service</label>
                <select name="service" class="inp">
                    <option value="">All services</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected(($filters['service'] ?? '') == $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label class="lbl">Status</label>
                <select name="status" class="inp">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="lbl">Date</label>
                <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="inp">
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Filter</button>
                <a href="{{ route('admin.tokens.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tokens table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Place</th>
                        <th>Service</th>
                        <th>Counter</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td>
                                <a href="{{ route('admin.tokens.show', $token) }}" class="font-bold brand-text">
                                    {{ $token->token_no }}
                                </a>
                            </td>
                            <td>{{ $token->created_at->format('d M Y') }}</td>
                            <td>
                                <div>{{ $token->user?->name ?? 'Walk-in' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $token->user?->phone ?? '—' }}</div>
                            </td>
                            <td>{{ $token->place?->name ?? '—' }}</td>
                            <td>{{ $token->service?->name ?? '—' }}</td>
                            <td>{{ $token->counter?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.tokens.show', $token) }}" class="text-xs font-semibold brand-text hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-slate-400 py-6">No tokens match the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $tokens->links() }}
    </div>
@endsection
