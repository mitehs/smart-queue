@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Welcome back, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-slate-500">Here's an overview of your queue activity.</p>
        </div>

        @if ($active)
            {{-- Active token card --}}
            <div class="card overflow-hidden mb-6">
                <div class="brand-bg text-white px-5 py-3 flex items-center justify-between">
                    <span class="font-semibold text-sm uppercase tracking-wider">Active Token</span>
                    <span class="badge b-{{ $active->status }}">{{ ucfirst(str_replace('_', ' ', $active->status)) }}</span>
                </div>
                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="text-center sm:text-left">
                        <div class="text-xs uppercase tracking-wider text-slate-400">Your token</div>
                        <div class="text-5xl font-extrabold brand-text">{{ $active->token_no }}</div>
                    </div>
                    <div class="flex-1 grid sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-400">Service</div>
                            <div class="font-semibold text-slate-700">{{ $active->service?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-400">Counter</div>
                            <div class="font-semibold text-slate-700">{{ $active->counter?->name ?? 'Not assigned yet' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-400">Place</div>
                            <div class="font-semibold text-slate-700">{{ $active->place?->name ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('queue.active') }}" class="btn btn-brand">Track live</a>
                        <a href="{{ route('queue.ticket', $active) }}" class="btn btn-outline">View ticket</a>
                    </div>
                </div>
            </div>
        @else
            {{-- No active token CTA --}}
            <div class="card p-8 mb-6 text-center brand-soft border-blue-100">
                <div class="text-4xl mb-2">🎫</div>
                <h2 class="text-lg font-bold text-slate-800">You have no active token</h2>
                <p class="text-sm text-slate-500 mt-1">Book a token from home and walk in right when it's your turn.</p>
                <a href="{{ route('queue.place') }}" class="btn btn-brand mt-4">Book a Token</a>
            </div>
        @endif

        {{-- KPI row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Total Tokens</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-emerald-500">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Completed</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['done'] }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-blue-500">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Today</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['today'] }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-amber-400">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Minutes Waited</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['waited']) }}</div>
            </div>
        </div>

        {{-- Recent tokens --}}
        <div class="card overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Recent tokens</h3>
                <a href="{{ route('user.history') }}" class="text-xs font-semibold brand-text hover:underline">View all</a>
            </div>

            @if ($recent->isEmpty())
                <div class="p-10 text-center">
                    <div class="text-3xl mb-2">📭</div>
                    <div class="font-semibold text-slate-700">No tokens yet</div>
                    <p class="text-sm text-slate-500 mt-1">Book your first token to get started.</p>
                    <a href="{{ route('queue.place') }}" class="btn btn-brand mt-4">Book a Token</a>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent as $t)
                                <tr>
                                    <td>
                                        <a href="{{ route('queue.ticket', $t) }}" class="font-bold brand-text hover:underline">{{ $t->token_no }}</a>
                                    </td>
                                    <td>{{ $t->issued_at?->format('d M Y, H:i') ?? $t->date }}</td>
                                    <td>{{ $t->place?->name ?? '—' }}</td>
                                    <td>{{ $t->service?->name ?? '—' }}</td>
                                    <td>{{ $t->counter?->name ?? '—' }}</td>
                                    <td><span class="badge b-{{ $t->status }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('queue.place') }}" class="card p-4 flex items-center gap-3 hover:border-blue-300 transition">
                <span class="text-xl">🎫</span>
                <span class="font-semibold text-sm text-slate-700">Book Token</span>
            </a>
            <a href="{{ route('user.history') }}" class="card p-4 flex items-center gap-3 hover:border-blue-300 transition">
                <span class="text-xl">🕘</span>
                <span class="font-semibold text-sm text-slate-700">History</span>
            </a>
            <a href="{{ route('queue.update') }}" class="card p-4 flex items-center gap-3 hover:border-blue-300 transition">
                <span class="text-xl">📝</span>
                <span class="font-semibold text-sm text-slate-700">Update Info</span>
            </a>
            <a href="{{ route('user.notifications') }}" class="card p-4 flex items-center gap-3 hover:border-blue-300 transition">
                <span class="text-xl">🔔</span>
                <span class="font-semibold text-sm text-slate-700">Notifications</span>
            </a>
        </div>
    </div>
@endsection
