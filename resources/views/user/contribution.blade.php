@extends('layouts.user')

@section('title', 'My Contribution')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">My contribution</h1>
            <p class="text-sm text-slate-500">A look at how often you visit and which services you use.</p>
        </div>

        @php $total = (int) $byService->sum('c'); @endphp

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Total Tokens</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($total) }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-blue-500">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Services Used</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $byService->count() }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-emerald-500">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Months This Year</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $monthly->count() }}</div>
            </div>
            <div class="card p-4 border-l-4 !border-l-amber-400">
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Busiest Service</div>
                <div class="text-lg font-bold text-amber-600 mt-1 truncate">{{ $byService->first()?->service?->name ?? '—' }}</div>
            </div>
        </div>

        @if ($total === 0)
            <div class="card p-12 text-center">
                <div class="text-3xl mb-2">📊</div>
                <div class="font-semibold text-slate-700">No data yet</div>
                <p class="text-sm text-slate-500 mt-1">Once you book your first token, your stats will appear here.</p>
                <a href="{{ route('queue.place') }}" class="btn btn-brand mt-4">Book a Token</a>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-6 mb-6">
                {{-- Monthly bar chart --}}
                <div class="card p-5 lg:col-span-2">
                    <h3 class="font-semibold text-slate-800 mb-4">Tokens per month (this year)</h3>
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>

                {{-- Doughnut --}}
                <div class="card p-5">
                    <h3 class="font-semibold text-slate-800 mb-4">By service</h3>
                    <canvas id="serviceChart" height="200"></canvas>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Tokens per service</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Code</th>
                                <th>Tokens</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byService as $row)
                                <tr>
                                    <td class="font-medium text-slate-700">{{ $row->service?->name ?? 'Unknown' }}</td>
                                    <td><span class="badge brand-soft brand-text">{{ $row->service?->code ?? '—' }}</span></td>
                                    <td class="font-bold">{{ $row->c }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-28 h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full brand-bg" style="width: {{ $total > 0 ? round($row->c / $total * 100) : 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-500">{{ $total > 0 ? round($row->c / $total * 100) : 0 }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthly = @json($monthly->toArray());
            const byService = @json($byService->map(fn ($r) => ['name' => $r->service?->name ?? 'Unknown', 'c' => (int) $r->c])->values()->toArray());

            const mCanvas = document.getElementById('monthlyChart');
            if (mCanvas && window.Chart && Object.keys(monthly).length) {
                new Chart(mCanvas, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(monthly),
                        datasets: [{
                            label: 'Tokens',
                            data: Object.values(monthly),
                            backgroundColor: '#1d4ed8',
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    },
                });
            }

            const sCanvas = document.getElementById('serviceChart');
            if (sCanvas && window.Chart && byService.length) {
                new Chart(sCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: byService.map(s => s.name),
                        datasets: [{
                            data: byService.map(s => s.c),
                            backgroundColor: ['#1d4ed8', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#1e3a8a', '#2563eb'],
                        }],
                    },
                    options: { plugins: { legend: { position: 'bottom' } } },
                });
            }
        });
    </script>
@endpush
