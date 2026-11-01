@extends('layouts.staff')

@section('title', 'Now Serving')
@section('page-title', 'Now Serving')
@section('page-subtitle', 'Counter ' . $counter->name . ' — your live working screen')
@section('active', 'tokens.active')

@section('header-actions')
    <a href="{{ route('staff.queue.index') }}" class="btn btn-ghost !py-1.5 !text-xs">Waiting List</a>
@endsection

@section('content')
<div id="activeWrap" data-current-id="{{ $current?->id }}">
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- NOW SERVING panel --}}
        <div class="lg:col-span-2">
            <div class="brand-bg rounded-2xl p-8 sm:p-12 text-white text-center shadow-lg">
                <div class="text-[11px] uppercase tracking-[.25em] text-white/70">Now Serving</div>

                @if ($current)
                    <div class="text-6xl sm:text-7xl font-bold mt-4">{{ $current->token_no }}</div>
                    <div class="text-xl sm:text-2xl font-semibold mt-4">{{ $current->user?->name ?? 'Walk-in customer' }}</div>
                    <div class="mt-3">
                        <span class="badge b-{{ $current->status }} !bg-white/95">{{ $current->status }}</span>
                    </div>
                    <div class="text-sm text-white/70 mt-4">
                        Called at {{ optional($current->called_at)->format('H:i') ?? '—' }}
                        @if ($current->user?->phone)
                            · 📞 {{ $current->user->phone }}
                        @endif
                    </div>
                @else
                    <div class="text-6xl sm:text-7xl font-bold mt-4 text-white/50">—</div>
                    <div class="text-xl font-semibold mt-4 text-white/80">No active customer</div>
                    <div class="text-sm text-white/60 mt-2">Press “Call Next” to serve the first waiting customer.</div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <form method="POST" action="{{ route('staff.tokens.call-next') }}" class="contents">
                    @csrf
                    <button type="submit" class="btn btn-success justify-center !py-4 !text-lg col-span-2 sm:col-span-1">▶ Call Next</button>
                </form>

                @if ($current)
                    <form method="POST" action="{{ route('staff.tokens.complete') }}" class="contents">
                        @csrf
                        <button type="submit" class="btn btn-brand justify-center !py-4 !text-lg">✓ Complete</button>
                    </form>
                    <form method="POST" action="{{ route('staff.tokens.skip') }}" class="contents"
                          onsubmit="return confirm('Skip customer {{ $current->token_no }}?')">
                        @csrf
                        <button type="submit" class="btn btn-warn justify-center !py-4 !text-lg">⏭ Skip</button>
                    </form>
                    <form method="POST" action="{{ route('staff.tokens.no-show') }}" class="contents"
                          onsubmit="return confirm('Mark customer {{ $current->token_no }} as no-show?')">
                        @csrf
                        <button type="submit" class="btn btn-danger justify-center !py-4 !text-lg">✗ No Show</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Side panel: next in line --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Next in line</h3>
                <span class="badge b-waiting">Waiting: {{ $waitingCount }}</span>
            </div>

            @if ($next)
                <div class="text-center py-6 rounded-xl brand-soft">
                    <div class="text-4xl font-bold brand-text">{{ $next->token_no }}</div>
                    <div class="text-sm font-semibold text-slate-700 mt-2">{{ $next->user?->name ?? 'Walk-in customer' }}</div>
                    <div class="text-xs text-slate-400 mt-1">Issued {{ optional($next->issued_at)->format('H:i') }}</div>
                </div>
            @else
                <div class="text-center py-10">
                    <div class="text-4xl font-bold text-slate-300">✓</div>
                    <p class="text-sm text-slate-400 mt-2">Queue is clear.</p>
                </div>
            @endif

            <div class="mt-5 space-y-1.5 text-xs border-t border-slate-100 pt-4">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Counter</span>
                    <span class="font-semibold text-slate-700">{{ $counter->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Service</span>
                    <span class="font-semibold text-slate-700">{{ $counter->service?->name ?? '—' }}</span>
                </div>
            </div>

            <a href="{{ route('staff.queue.index') }}" class="btn btn-ghost w-full justify-center mt-4">Full waiting list</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function poll() {
        setTimeout(function () {
            fetch('{{ route("staff.tokens.status") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const rendered = document.getElementById('activeWrap').dataset.currentId || '';
                const current = (data && data.current) ? String(data.current.id) : '';
                if (rendered !== current) {
                    location.reload();
                    return;
                }
                poll();
            })
            .catch(function () { poll(); });
        }, 4000);
    })();
</script>
@endpush
