@extends('layouts.staff')

@section('title', 'My Counter')
@section('page-title', 'My Counter')
@section('page-subtitle', 'Live overview of your queue today')
@section('active', 'dashboard')

@section('content')
    {{-- Counter banner --}}
    <div class="card p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Counter</div>
            <h2 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $counter->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">
                {{ $counter->location ?? '—' }} · <span class="brand-text font-semibold">{{ $counter->service?->name ?? '—' }}</span>
            </p>
        </div>
        <a href="{{ route('staff.tokens.active') }}" class="btn btn-brand !px-5 !py-3 !text-base shrink-0">
            Open Now Serving ▶
        </a>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="kpi border-l-4 !border-l-amber-400">
            <div class="n text-amber-600">{{ $stats['waiting'] }}</div>
            <div class="t">Waiting</div>
        </div>
        <div class="kpi border-l-4 !border-l-blue-500">
            <div class="n text-blue-600">{{ $stats['serving'] }}</div>
            <div class="t">In Service</div>
        </div>
        <div class="kpi border-l-4 !border-l-emerald-500">
            <div class="n text-emerald-600">{{ $stats['done'] }}</div>
            <div class="t">Completed</div>
        </div>
        <div class="kpi border-l-4 !border-l-red-400">
            <div class="n text-red-500">{{ $stats['missed'] }}</div>
            <div class="t">Missed</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Line chart --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">My queue — last 7 days</h3>
                <span class="text-xs text-slate-400">{{ now()->format('D, d M Y') }}</span>
            </div>
            <canvas id="queueChart" height="110"></canvas>
        </div>

        {{-- Current customer --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Now serving</h3>
            @if ($current)
                <div class="text-center py-4">
                    <div class="text-5xl font-bold brand-text">{{ $current->token_no }}</div>
                    <div class="text-sm font-semibold text-slate-700 mt-2">{{ $current->user?->name ?? '—' }}</div>
                    <span class="badge b-{{ $current->status }} mt-2">{{ $current->status }}</span>
                    <div class="text-xs text-slate-400 mt-3">Called {{ optional($current->called_at)->format('H:i') ?? '—' }}</div>
                </div>
            @else
                <div class="text-center py-10">
                    <div class="text-4xl font-bold text-slate-300">—</div>
                    <p class="text-sm text-slate-400 mt-2">No active customer. Call the next one!</p>
                </div>
            @endif
            <a href="{{ route('staff.tokens.active') }}" class="btn btn-ghost w-full justify-center mt-2">Go to Now Serving</a>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Recent activity</h3>
            <a href="{{ route('staff.queue.today') }}" class="text-xs font-semibold brand-text hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent as $token)
                        <tr>
                            <td>
                                <a href="{{ route('staff.tokens.show', $token) }}" class="font-bold brand-text">
                                    {{ $token->token_no }}
                                </a>
                            </td>
                            <td>{{ $token->user?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-slate-400 py-6">No activity yet today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const brand = '#0f766e';

    new Chart(document.getElementById('queueChart'), {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [{
                label: 'Tokens',
                data: @json($chart['data']),
                borderColor: brand,
                backgroundColor: 'rgba(15,118,110,.12)',
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
</script>
@endpush
