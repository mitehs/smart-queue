@extends('layouts.admin')

@section('title', 'Report Analytics')
@section('page-title', 'Report Analytics')
@section('page-subtitle', 'Service and counter performance over a date range')
@section('active', 'reports.analytics')

@section('content')
    {{-- Date filter --}}
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-44">
                <label class="lbl">From</label>
                <input type="date" name="from" value="{{ $from }}" class="inp">
            </div>
            <div class="w-full sm:w-44">
                <label class="lbl">To</label>
                <input type="date" name="to" value="{{ $to }}" class="inp">
            </div>
            <div class="w-full sm:w-52">
                <label class="lbl">Place</label>
                <select name="place" class="inp">
                    <option value="">All places</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" @selected(request('place') == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Apply</button>
            </div>
        </form>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Avg Wait</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $avgWait }}<span class="text-sm font-medium text-slate-400"> min</span></div>
        </div>
        <div class="card p-4 border-l-4 !border-l-blue-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Avg Service Time</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $avgServiceTime }}<span class="text-sm font-medium text-slate-400"> min</span></div>
        </div>
        <div class="card p-4 border-l-4 !border-l-emerald-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Total Tokens</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ (int) $byService->sum('total') }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-amber-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Counters Tracked</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $byCounter->count() }}</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Tokens by service</h3>
            <canvas id="serviceChart" height="170"></canvas>
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Tokens by counter</h3>
            <canvas id="counterChart" height="170"></canvas>
        </div>
    </div>

    <div class="card p-5 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4">Tokens per day</h3>
        <canvas id="dailyChart" height="90"></canvas>
    </div>

    {{-- Tables --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Per service</h3>
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
                            <tr><td colspan="4" class="text-center text-slate-400 py-6">No service data in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Per counter</h3>
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
                            <tr><td colspan="6" class="text-center text-slate-400 py-6">No counter data in this range.</td></tr>
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
    const ticks = { color: '#94a3b8', font: { size: 11 } };

    new Chart(document.getElementById('serviceChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byService->pluck('name')->values()),
            datasets: [{
                data: @json($byService->pluck('total')->values()),
                backgroundColor: ['#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: '#64748b', font: { size: 11 } } } }, cutout: '65%' }
    });

    new Chart(document.getElementById('counterChart'), {
        type: 'bar',
        data: {
            labels: @json($byCounter->pluck('name')->values()),
            datasets: [{
                label: 'Tokens',
                data: @json($byCounter->pluck('total')->values()),
                backgroundColor: brand,
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, ...ticks }, grid: { color: '#f1f5f9' } },
                x: { ticks, grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($chart['data']),
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
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, ...ticks }, grid: { color: '#f1f5f9' } },
                x: { ticks, grid: { display: false } }
            }
        }
    });
</script>
@endpush
