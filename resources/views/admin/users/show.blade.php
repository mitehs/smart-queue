@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('page-subtitle', 'Customer profile and token history')
@section('active', 'users')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold brand-text hover:underline">← Back to users</a>

    {{-- Profile header --}}
    <div class="card p-6 mt-3 mb-6">
        <div class="flex flex-wrap items-center gap-5">
            <div class="w-16 h-16 rounded-full brand-bg text-white grid place-items-center text-2xl font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <p class="text-sm text-slate-500">{{ $user->phone ?? 'No phone' }} · {{ $user->place?->name ?? 'No place' }}</p>
            </div>
            <div class="ml-auto text-right">
                @if ($user->is_active)
                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                @else
                    <span class="badge bg-red-100 text-red-700">Inactive</span>
                @endif
                <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-2">Member since</div>
                <div class="text-sm font-semibold text-slate-700">{{ $user->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6 pt-5 border-t border-slate-100">
            <div class="rounded-lg bg-slate-50 p-4 text-center">
                <div class="text-2xl font-bold text-slate-800">{{ $user->queue_tokens_count }}</div>
                <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-1">Total Tokens</div>
            </div>
            <div class="rounded-lg bg-slate-50 p-4 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $user->queueTokens()->where('status', 'done')->count() }}</div>
                <div class="text-[11px] uppercase tracking-wider text-slate-400 mt-1">Completed</div>
            </div>
        </div>
    </div>

    {{-- Tokens table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Token history</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Counter</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th>Completed</th>
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
                            <td>{{ $token->service?->name ?? '—' }}</td>
                            <td>{{ $token->counter?->name ?? '—' }}</td>
                            <td><span class="badge b-{{ $token->status }}">{{ $token->status }}</span></td>
                            <td>{{ optional($token->issued_at)->format('H:i') }}</td>
                            <td>{{ optional($token->completed_at)->format('H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-slate-400 py-6">No tokens yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $tokens->links() }}
    </div>
@endsection
