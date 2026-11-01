@extends('layouts.user')

@section('title', 'Token Ticket')

@section('content')
    <div class="max-w-md mx-auto px-4 sm:px-6 py-8">
        {{-- Ticket --}}
        <div class="card overflow-hidden">
            {{-- Header --}}
            <div class="brand-bg text-white px-5 py-4 text-center">
                <div class="font-bold text-lg">{{ $token->place?->name ?? 'Smart Queue' }}</div>
                @if ($token->place?->address)
                    <div class="text-xs text-blue-100 mt-0.5">{{ $token->place->address }}</div>
                @endif
                @if ($token->place?->phone)
                    <div class="text-xs text-blue-100">📞 {{ $token->place->phone }}</div>
                @endif
            </div>

            <div class="p-6 text-center">
                <div class="text-[11px] uppercase tracking-widest text-slate-400">Queue Token</div>
                <div class="text-6xl font-extrabold brand-text mt-1">{{ $token->token_no }}</div>
                <div class="mt-2">
                    <span class="badge b-{{ $token->status }}">{{ ucfirst(str_replace('_', ' ', $token->status)) }}</span>
                </div>
            </div>

            <div class="border-t border-dashed border-slate-200 mx-6"></div>

            {{-- Details --}}
            <div class="p-6 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Customer</span>
                    <span class="font-semibold text-slate-800">{{ $token->user?->name ?? auth()->user()->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Service</span>
                    <span class="font-semibold text-slate-800">{{ $token->service?->name ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Counter</span>
                    <span class="font-semibold text-slate-800">
                        {{ $token->counter?->name ?? 'TBA' }}
                        @if ($token->counter?->staff)
                            <span class="text-xs font-normal text-slate-500">({{ $token->counter->staff->name }})</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Date</span>
                    <span class="font-semibold text-slate-800">{{ $token->date }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Issued</span>
                    <span class="font-semibold text-slate-800">{{ $token->issued_at?->format('H:i') ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">People ahead</span>
                    <span class="font-semibold text-slate-800">{{ $token->isOpen() ? $peopleAhead : '—' }}</span>
                </div>
            </div>

            <div class="border-t border-dashed border-slate-200 mx-6"></div>

            <div class="p-5 text-center text-xs text-slate-400">
                Please keep this ticket until your service is complete. © {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-wrap justify-center gap-3 no-print">
            <button type="button" onclick="window.print()" class="btn btn-brand">🖨️ Print</button>
            <a href="{{ route('queue.active') }}" class="btn btn-outline">Back to Live Tracking</a>
        </div>
    </div>
@endsection

@push('head')
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
@endpush
