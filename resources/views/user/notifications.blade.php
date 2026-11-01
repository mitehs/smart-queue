@extends('layouts.user')

@section('title', 'Notifications')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Notifications</h1>
            <p class="text-sm text-slate-500">Queue alerts and your most recent tickets.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Recent tickets --}}
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="card p-5">
                    <h3 class="font-semibold text-slate-800 mb-4">Your recent tickets</h3>

                    @if ($active->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-2xl mb-2">🎫</div>
                            <p class="text-sm text-slate-500">No tickets yet. Book your first token to get started.</p>
                            <a href="{{ route('queue.place') }}" class="btn btn-brand mt-3 !py-1.5 !text-xs">Book a Token</a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($active as $t)
                                <a href="{{ route('queue.ticket', $t) }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 transition">
                                    <div>
                                        <div class="font-bold brand-text">{{ $t->token_no }}</div>
                                        <div class="text-xs text-slate-500">{{ $t->service?->name ?? '—' }} · {{ $t->created_at?->diffForHumans() }}</div>
                                    </div>
                                    <span class="badge b-{{ $t->status }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notifications --}}
            <div class="lg:col-span-2 order-1 lg:order-2">
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-semibold text-slate-800">Notifications</h3>
                    </div>

                    @if ($notifications->isEmpty())
                        <div class="p-12 text-center">
                            <div class="text-3xl mb-2">🔔</div>
                            <div class="font-semibold text-slate-700">Nothing here yet</div>
                            <p class="text-sm text-slate-500 mt-1">You'll be notified when your token is called or something important happens.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-100">
                            @foreach ($notifications as $n)
                                <li class="px-5 py-4 flex items-start gap-3 {{ is_null($n->read_at) ? 'bg-blue-50/40' : '' }}">
                                    <span class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0 {{ is_null($n->read_at) ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm text-slate-700 break-words">
                                            {{ is_array($n->data) ? ($n->data['message'] ?? json_encode($n->data)) : $n->data }}
                                        </p>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                            <span>{{ $n->created_at->diffForHumans() }}</span>
                                            @if (is_null($n->read_at))
                                                <span class="font-semibold text-blue-600">Unread</span>
                                            @else
                                                <span>Read</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="px-5 py-4 border-t border-slate-100">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
