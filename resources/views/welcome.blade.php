<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Skip the wait, just check in — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: figtree, ui-sans-serif, system-ui, sans-serif; }
        .l-card { background:#fff; border:1px solid #e5e7eb; border-radius:1rem; box-shadow:0 1px 2px rgba(16,24,40,.04); }
        .l-btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:.7rem; padding:.7rem 1.4rem; font-weight:600; font-size:.95rem; transition:.15s; }
        .l-btn-brand { background:#1d4ed8; color:#fff; box-shadow:0 10px 22px -12px rgba(29,78,216,.9); }
        .l-btn-brand:hover { background:#1e3a8a; }
        .l-btn-outline { background:#fff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .l-btn-outline:hover { background:#eff6ff; }
        .l-link { color:#1d4ed8; font-weight:600; }
        .l-link:hover { text-decoration:underline; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-blue-700 text-white grid place-items-center font-bold">Q</span>
                <span class="font-semibold text-slate-800">Smart Queue</span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm">
                <a href="#how" class="px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">How it works</a>
                <a href="#locations" class="px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Locations</a>
                <a href="{{ route('help') }}" class="px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Help</a>
                <a href="{{ route('contact') }}" class="px-3 py-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Contact</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="l-btn l-btn-outline !py-1.5 !px-4 !text-sm">Log in</a>
                <a href="{{ route('register') }}" class="l-btn l-btn-brand !py-1.5 !px-4 !text-sm">Register</a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50 to-slate-50"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-700 rounded-full px-3 py-1 mb-4">Smart Queue Management</span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight">Skip the wait,<br> just check in.</h1>
                <p class="mt-5 text-lg text-slate-600 max-w-lg">
                    Take a token from the comfort of your home, track the queue live, and walk in right when it's your turn. No lines, no wasted hours.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('queue.place') }}" class="l-btn l-btn-brand">Book a Token Now</a>
                    <a href="{{ route('login') }}" class="l-btn l-btn-outline">Log in</a>
                </div>
                <div class="mt-6 flex items-center gap-2 text-sm text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live queues updated in real time
                </div>
            </div>

            {{-- Illustration --}}
            <div class="hidden lg:block">
                <svg viewBox="0 0 420 320" class="w-full max-w-md mx-auto" role="img" aria-label="Token card illustration">
                    <circle cx="210" cy="160" r="150" fill="#eff6ff"/>
                    <circle cx="210" cy="160" r="110" fill="#dbeafe"/>
                    <!-- token card -->
                    <g transform="rotate(-6 210 160)">
                        <rect x="90" y="70" width="240" height="150" rx="18" fill="#fff" stroke="#bfdbfe" stroke-width="3"/>
                        <rect x="90" y="70" width="240" height="46" rx="18" fill="#1d4ed8"/>
                        <rect x="90" y="100" width="240" height="16" fill="#1d4ed8"/>
                        <text x="210" y="101" text-anchor="middle" fill="#fff" font-family="figtree, sans-serif" font-size="18" font-weight="700">YOUR TOKEN</text>
                        <text x="210" y="175" text-anchor="middle" fill="#1d4ed8" font-family="figtree, sans-serif" font-size="52" font-weight="800">A-42</text>
                        <text x="210" y="203" text-anchor="middle" fill="#64748b" font-family="figtree, sans-serif" font-size="14">You're 3rd in line</text>
                    </g>
                    <!-- clock -->
                    <g transform="translate(300 205)">
                        <circle cx="0" cy="0" r="38" fill="#1d4ed8"/>
                        <circle cx="0" cy="0" r="30" fill="#fff"/>
                        <line x1="0" y1="0" x2="0" y2="-18" stroke="#1d4ed8" stroke-width="4" stroke-linecap="round"/>
                        <line x1="0" y1="0" x2="13" y2="8" stroke="#1d4ed8" stroke-width="4" stroke-linecap="round"/>
                    </g>
                    <!-- check bubble -->
                    <g transform="translate(80 220)">
                        <circle cx="0" cy="0" r="28" fill="#059669"/>
                        <path d="M-11 1 L-3 9 L12 -8" stroke="#fff" stroke-width="5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>
        </div>
    </section>

    {{-- Stats strip --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-8 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="l-card p-6 text-center">
                <div class="text-4xl font-extrabold text-blue-700">{{ number_format($todayServed) }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-600">Served today</div>
                <div class="text-xs text-slate-400">across every location</div>
            </div>
            <div class="l-card p-6 text-center">
                <div class="text-4xl font-extrabold text-blue-700">{{ count($places) }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-600">Locations</div>
                <div class="text-xs text-slate-400">banks, hospitals, offices &amp; more</div>
            </div>
            <div class="l-card p-6 text-center">
                <div class="text-4xl font-extrabold text-emerald-600 flex items-center justify-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </div>
                <div class="mt-1 text-sm font-semibold text-slate-600">Queue board</div>
                <div class="text-xs text-slate-400">updates as tokens are called</div>
            </div>
        </div>
    </section>

    {{-- Now serving board --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="flex items-end justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Now serving</h2>
                <p class="text-sm text-slate-500">Live board — who's being called right now.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> LIVE
            </span>
        </div>
        <div class="l-card overflow-hidden">
            @if ($nowServing->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-4xl mb-2">☕</div>
                    <div class="font-semibold text-slate-700">All clear right now</div>
                    <div class="text-sm text-slate-500 mt-1">No tokens are being served at the moment. Book a token and beat the rush.</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-left text-[11px] uppercase tracking-wider">
                                <th class="px-5 py-3">Counter</th>
                                <th class="px-5 py-3">Token</th>
                                <th class="px-5 py-3">Service</th>
                                <th class="px-5 py-3">Place</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($nowServing as $t)
                                <tr>
                                    <td class="px-5 py-3 text-slate-600">{{ $t->counter?->name ?? '—' }}</td>
                                    <td class="px-5 py-3 font-extrabold text-blue-700">{{ $t->token_no }}</td>
                                    <td class="px-5 py-3 text-slate-700">{{ $t->service?->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $t->place?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    {{-- Locations --}}
    <section id="locations" class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <h2 class="text-2xl font-bold text-slate-900">Locations</h2>
        <p class="text-sm text-slate-500 mb-6">Pick a place and book your token in seconds.</p>

        @if ($places->isEmpty())
            <div class="l-card p-10 text-center text-slate-500">No locations available yet. Please check back soon.</div>
        @else
            @php $icons = ['bank' => '🏦', 'government' => '🏛️', 'hospital' => '🏥', 'office' => '🏢', 'service' => '🔧']; @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($places as $place)
                    <div class="l-card p-5 flex flex-col">
                        <div class="flex items-start justify-between">
                            <span class="text-3xl">{{ $icons[$place->type] ?? '🏢' }}</span>
                            <span class="text-[11px] font-bold uppercase tracking-wide bg-blue-100 text-blue-700 rounded-full px-2.5 py-1">{{ ucfirst($place->type) }}</span>
                        </div>
                        <h3 class="mt-3 font-semibold text-slate-800">{{ $place->name }}</h3>
                        @if ($place->address)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $place->address }}</p>
                        @endif
                        <div class="mt-2 text-sm text-slate-500">{{ $place->services_count }} service{{ $place->services_count === 1 ? '' : 's' }} available</div>
                        <a href="{{ route('queue.place') }}" class="l-btn l-btn-outline mt-4 w-full">Book here</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- How it works --}}
    <section id="how" class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
        <p class="text-sm text-slate-500 mb-6">Four simple steps — no app to install.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['n' => 1, 't' => 'Choose place', 'd' => 'Pick the bank, hospital or office you are visiting.'],
                ['n' => 2, 't' => 'Pick service', 'd' => 'Select the service you need and a free counter.'],
                ['n' => 3, 't' => 'Get token', 'd' => 'Receive your token number instantly with an estimated wait.'],
                ['n' => 4, 't' => 'Get called', 'd' => 'Track the queue live and walk in right when it is your turn.'],
            ] as $step)
                <div class="l-card p-5">
                    <span class="w-10 h-10 rounded-xl bg-blue-700 text-white grid place-items-center font-bold">{{ $step['n'] }}</span>
                    <h3 class="mt-3 font-semibold text-slate-800">{{ $step['t'] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $step['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Features --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <h2 class="text-2xl font-bold text-slate-900">Why Smart Queue</h2>
        <p class="text-sm text-slate-500 mb-6">Everything you need to never wait in line again.</p>
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="l-card p-6">
                <div class="text-2xl">⚡</div>
                <h3 class="mt-3 font-semibold text-slate-800">Real-time updates</h3>
                <p class="mt-1 text-sm text-slate-500">Your token screen refreshes automatically every few seconds, so you always know exactly where you stand.</p>
            </div>
            <div class="l-card p-6">
                <div class="text-2xl">🔔</div>
                <h3 class="mt-3 font-semibold text-slate-800">SMS &amp; notifications</h3>
                <p class="mt-1 text-sm text-slate-500">Get notified the moment your token is called — right inside the app, so you never miss your turn.</p>
            </div>
            <div class="l-card p-6">
                <div class="text-2xl">📊</div>
                <h3 class="mt-3 font-semibold text-slate-800">Reports &amp; analytics</h3>
                <p class="mt-1 text-sm text-slate-500">See how long you typically wait, your visit history and per-service stats — for you and for the business.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="rounded-2xl bg-blue-700 text-white p-8 sm:p-12 text-center shadow-xl shadow-blue-200">
            <h2 class="text-2xl sm:text-3xl font-extrabold">Ready to skip the line?</h2>
            <p class="mt-2 text-blue-100 max-w-xl mx-auto">Create a free account and book your first token in under a minute.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="l-btn bg-white text-blue-700 hover:bg-blue-50">Register free</a>
                <a href="{{ route('queue.place') }}" class="l-btn border border-blue-400 text-white hover:bg-blue-800">Book a Token Now</a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-700 text-white grid place-items-center font-bold text-sm">Q</span>
                <span>© {{ date('Y') }} Smart Queue — Skip the wait, just check in.</span>
            </div>
            <div class="flex flex-wrap justify-center gap-5">
                <a href="{{ route('help') }}" class="hover:text-slate-900">Help</a>
                <a href="{{ route('contact') }}" class="hover:text-slate-900">Contact</a>
                <a href="{{ route('login') }}" class="hover:text-slate-900">Login</a>
                <a href="{{ route('register') }}" class="hover:text-slate-900">Register</a>
            </div>
        </div>
    </footer>

</body>
</html>
