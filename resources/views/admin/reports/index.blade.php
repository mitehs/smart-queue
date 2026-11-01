@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-subtitle', 'Queue volume for any date range')
@section('active', 'reports.index')

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
                <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to, 'place' => request('place')]) }}" class="btn btn-ghost">⬇ Export CSV</a>
            </div>
        </form>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Total</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-emerald-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Completed</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['done'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-amber-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Waiting</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['waiting'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-red-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Missed</div>
            <div class="text-2xl font-bold text-red-500 mt-1">{{ $stats['missed'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-red-400">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Cancelled</div>
            <div class="text-2xl font-bold text-red-500 mt-1">{{ $stats['cancelled'] }}</div>
        </div>
        <div class="card p-4 border-l-4 !border-l-blue-500">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Completion</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['completion'] }}%</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Daily volume</h3>
            <canvas id="dailyChart" height="120"></canvas>
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Peak hours</h3>
            <canvas id="peakChart" height="120"></canvas>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Tokens in range</h3>
            <span class="text-xs text-slate-400">Showing up to 200 records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Counter</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($table as $token)
                        <tr>
                            <td class="font-bold brand-text">{{ $token->token_no }}</td>
                            <td>{{ $token->created_at->format('d M Y') }}</td>
                            <td>{{ $token->user?->name ?? 'Walk-in' }}</td>
                            <td>{{ $token->service?->name ?? '—' }}</td>
                            <td>{{ $token->counter?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ optional($token->completed_at)->format('H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-slate-400 py-6">No tokens in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const brand = '#1d4ed8';
    const ticks = { color: '#94a3b8', font: { size: 11 } };

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

    new Chart(document.getElementById('peakChart'), {
        type: 'bar',
        data: {
            labels: @json($peak['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($peak['data']),
                backgroundColor: '#2563eb',
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
</script>
@endpush
