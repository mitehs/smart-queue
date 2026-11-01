@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Live overview of today\'s queue activity')
@section('active', 'dashboard')

@section('header-actions')
    <form method="GET" class="flex items-center gap-2">
        <select name="place" onchange="this.form.submit()" class="inp !w-auto !py-1.5 !text-xs">
            <option value="">All places</option>
            @foreach ($places as $place)
                <option value="{{ $place->id }}" @selected($activePlace == $place->id)>{{ $place->name }}</option>
            @endforeach
        </select>
    </form>
@endsection

@section('content')
    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Tokens Today</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-amber-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Waiting</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['waiting'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-blue-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">In Service</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['called'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-emerald-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Completed</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['done'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-red-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Missed</div>
            <div class="text-2xl font-bold text-red-500 mt-1">{{ $stats['no_show'] }}</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Line chart --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Tokens per hour</h3>
                <span class="text-xs text-slate-400">{{ now()->format('D, d M Y') }}</span>
            </div>
            <canvas id="hourlyChart" height="110"></canvas>
        </div>

        {{-- Donut --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Status split</h3>
            <canvas id="statusChart" height="180"></canvas>
            <div class="mt-4 space-y-1.5 text-xs">
                @foreach ($statusChart as $label => $value)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">{{ $label }}</span>
                        <span class="font-semibold text-slate-700">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Recent tokens --}}
        <div class="card lg:col-span-2 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Recent tokens</h3>
                <a href="{{ route('admin.tokens.index') }}" class="text-xs font-semibold brand-text hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Token</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Counter</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $token)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.tokens.show', $token) }}" class="font-bold brand-text">
                                        {{ $token->token_no }}
                                    </a>
                                </td>
                                <td>{{ $token->user?->name ?? '—' }}</td>
                                <td>{{ $token->service?->name ?? '—' }}</td>
                                <td>{{ $token->counter?->name ?? '—' }}</td>
                                <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                                <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-400 py-6">No tokens issued yet today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Counter load --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Counter load</h3>
            <div class="space-y-3">
                @forelse ($counterLoad as $counter)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg bg-slate-50">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-700 truncate">{{ $counter->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">
                                {{ $counter->staff?->name ?? 'Unassigned' }} · {{ $counter->service?->name ?? '—' }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-lg font-bold text-slate-800">{{ $counter->today_count }}</div>
                            <div class="text-[10px] uppercase text-slate-400">today</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No counters yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const brand = '#1d4ed8';

    new Chart(document.getElementById('hourlyChart'), {
        type: 'line',
        data: {
            labels: @json($hourly['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($hourly['data']),
                borderColor: brand,
                backgroundColor: 'rgba(29,78,216,.12)',
                fill: true,
                tension: .4,
                pointRadius: 3,
                pointBackgroundColor: brand
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($statusChart)),
            datasets: [{
                data: @json(array_values($statusChart)),
                backgroundColor: ['#f59e0b', '#2563eb', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { display: false } }, cutout: '68%' }
    });
</script>
@endpush
