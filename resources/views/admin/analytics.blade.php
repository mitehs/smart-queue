@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('page-subtitle', 'Queue performance across services, counters and time')
@section('active', 'analytics')

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
    @php
        $totalTokens = (int) $byService->sum('total');
        $serviceLabels = $byService->pluck('name')->values();
        $serviceTotals = $byService->pluck('total')->values();
        $counterLabels = $byCounter->pluck('name')->values();
        $counterTotals = $byCounter->pluck('total')->values();
    @endphp

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Avg Wait</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $avgWait }}<span class="text-sm font-medium text-slate-400"> min</span></div>
        </div>
        <div class="card p-4 border-l-4 !border-l-blue-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Avg Service Time</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $avgService }}<span class="text-sm font-medium text-slate-400"> min</span></div>
        </div>
        <div class="card p-4 border-l-4 !border-l-emerald-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Total Services</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $byService->count() }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-amber-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Total Counters</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $byCounter->count() }}</div>
        </div>
    </div>

    {{-- Time charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Tokens per day</h3>
                <span class="text-xs text-slate-400">Last {{ count($weekly['labels']) }} days</span>
            </div>
            <canvas id="weeklyChart" height="110"></canvas>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Tokens per hour</h3>
                <span class="text-xs text-slate-400">{{ now()->format('D, d M Y') }}</span>
            </div>
            <canvas id="hourlyChart" height="110"></canvas>
        </div>
    </div>

    {{-- Distribution charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Tokens by service</h3>
            <canvas id="serviceChart" height="160"></canvas>
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Tokens by counter</h3>
            <canvas id="counterChart" height="160"></canvas>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Service performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Total</th>
                            <th>Completed</th>
                            <th>Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byService as $service)
                            @php $pct = $service->total > 0 ? round($service->done_count / $service->total * 100) : 0; @endphp
                            <tr>
                                <td class="font-semibold">{{ $service->name }}</td>
                                <td>{{ $service->total }}</td>
                                <td>{{ $service->done_count }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full brand-bg" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-600">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-400 py-6">No services yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Counter performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Counter</th>
                            <th>Service</th>
                            <th>Staff</th>
                            <th>Total</th>
                            <th>Completed</th>
                            <th>Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byCounter as $counter)
                            @php $pct = $counter->total > 0 ? round($counter->done_count / $counter->total * 100) : 0; @endphp
                            <tr>
                                <td class="font-semibold">{{ $counter->name }}</td>
                                <td>{{ $counter->service?->name ?? '—' }}</td>
                                <td>{{ $counter->staff?->name ?? 'Unassigned' }}</td>
                                <td>{{ $counter->total }}</td>
                                <td>{{ $counter->done_count }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full brand-bg" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-600">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-400 py-6">No counters yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const brand = '#1d4ed8';
    const grid = { color: '#f1f5f9' };
    const ticks = { color: '#94a3b8', font: { size: 11 } };

    new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: {
            labels: @json($weekly['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($weekly['data']),
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
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, ...ticks }, grid }, x: { ticks, grid } }
        }
    });

    new Chart(document.getElementById('hourlyChart'), {
        type: 'line',
        data: {
            labels: @json($hourly['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($hourly['data']),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,.12)',
                fill: true,
                tension: .4,
                pointRadius: 3,
                pointBackgroundColor: '#2563eb'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, ...ticks }, grid }, x: { ticks, grid } }
        }
    });

    new Chart(document.getElementById('serviceChart'), {
        type: 'doughnut',
        data: {
            labels: @json($serviceLabels),
            datasets: [{
                data: @json($serviceTotals),
                backgroundColor: ['#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: '#64748b', font: { size: 11 } } } }, cutout: '65%' }
    });

    new Chart(document.getElementById('counterChart'), {
        type: 'bar',
        data: {
            labels: @json($counterLabels),
            datasets: [{
                label: 'Tokens',
                data: @json($counterTotals),
                backgroundColor: brand,
                borderRadius: 6,
                barThickness: 18
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1, ...ticks }, grid }, y: { ticks, grid: { display: false } } }
        }
    });
</script>
@endpush
