<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/chart.umd.js') }}"></script>
    @stack('head')
    <style>
        :root { --brand: #0f766e; --brand-dark: #115e59; --brand-soft: #f0fdfa; }
        .sidebar-fixed { background: linear-gradient(180deg, #0b3d3a 0%, #0f766e 100%); }
        .side-link { display: flex; align-items: center; gap: .75rem; padding: .6rem .75rem; border-radius: .6rem; color: rgba(255,255,255,.8); transition: .15s; }
        .side-link:hover { background: rgba(255,255,255,.10); color: #fff; }
        .side-link-active { background: #14b8a6 !important; color: #fff !important; box-shadow: 0 6px 16px -6px rgba(20,184,166,.8); }
        .brand-bg { background: var(--brand) !important; }
        .brand-text { color: var(--brand); }
        .brand-soft { background: var(--brand-soft); }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius: .9rem; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
        .btn { display:inline-flex; align-items:center; gap:.4rem; border-radius:.55rem; padding:.5rem .9rem; font-size:.85rem; font-weight:600; transition:.15s; }
        .btn-brand { background: var(--brand); color:#fff; } .btn-brand:hover { background: var(--brand-dark); }
        .btn-ghost { background:#f3f4f6; color:#374151; } .btn-ghost:hover { background:#e5e7eb; }
        .btn-danger { background:#dc2626; color:#fff; } .btn-danger:hover { background:#b91c1c; }
        .btn-success { background:#059669; color:#fff; } .btn-success:hover { background:#047857; }
        .btn-warn { background:#d97706; color:#fff; } .btn-warn:hover { background:#b45309; }
        .badge { display:inline-block; padding:.15rem .55rem; border-radius:999px; font-size:.7rem; font-weight:700; letter-spacing:.02em; }
        .b-waiting { background:#fef3c7; color:#92400e; }
        .b-called { background:#dbeafe; color:#1e40af; }
        .b-serving { background:#d1fae5; color:#065f46; }
        .b-done { background:#e0e7ff; color:#3730a3; }
        .b-skipped, .b-no_show, .b-cancelled { background:#fee2e2; color:#991b1b; }
        .tbl { width:100%; border-collapse:collapse; font-size:.85rem; }
        .tbl th { text-align:left; padding:.7rem .8rem; background:#f8fafc; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; }
        .tbl td { padding:.7rem .8rem; border-bottom:1px solid #f1f5f9; color:#334155; }
        .tbl tbody tr:hover { background:#f8fafc; }
        .inp { width:100%; border:1px solid #d1d5db; border-radius:.55rem; padding:.5rem .75rem; font-size:.875rem; background:#fff; }
        .inp:focus { outline:none; border-color: var(--brand); box-shadow:0 0 0 3px rgba(15,118,110,.15); }
        .lbl { display:block; font-size:.78rem; font-weight:600; color:#475569; margin-bottom:.3rem; }
        .kpi { background:#fff; border:1px solid #e5e7eb; border-radius:.9rem; padding:1rem 1.1rem; }
        .kpi .n { font-size:1.6rem; font-weight:700; color:#0f172a; line-height:1.1; }
        .kpi .t { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-top:.25rem; }
        @media (max-width: 1024px) { #sidebar { position: fixed; z-index: 50; height: 100%; transform: translateX(-100%); transition: transform .25s; } #sidebar.open { transform: none; } }
        @media print { #sidebar, .print\:hidden { display:none !important; } }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
@php
    $menu = [
        '' => [
            ['label' => 'My Counter', 'url' => route('staff.dashboard'), 'active' => 'dashboard', 'icon' => '&#9632;'],
            ['label' => 'Now Serving', 'url' => route('staff.tokens.active'), 'active' => 'tokens.active', 'icon' => '&#9654;'],
        ],
        'My Queue' => [
            ['label' => 'Waiting List', 'url' => route('staff.queue.index'), 'active' => 'queue.index', 'icon' => '&#9884;'],
            ['label' => "Today's Tokens", 'url' => route('staff.queue.today'), 'active' => 'queue.today', 'icon' => '&#9776;'],
            ['label' => 'Upcoming', 'url' => route('staff.queue.upcoming'), 'active' => 'queue.upcoming', 'icon' => '&#9650;'],
            ['label' => 'History', 'url' => route('staff.queue.history'), 'active' => 'queue.history', 'icon' => '&#8635;'],
        ],
        'Account' => [
            ['label' => 'Profile', 'url' => route('profile.edit'), 'active' => 'profile.edit', 'icon' => '&#9823;'],
        ],
    ];
    $active = trim($__env->yieldContent('active', ''));
@endphp
<div class="flex min-h-screen">
    @include('layouts.partials.sidebar', ['menu' => $menu, 'active' => $active])

    <div class="flex-1 min-w-0 flex flex-col">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 print:hidden">
            <div class="flex items-center gap-3">
                <button type="button" onclick="document.getElementById('sidebar').classList.toggle('open')"
                        class="lg:hidden text-slate-500 hover:text-slate-800 text-xl">☰</button>
                <div>
                    <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'My Counter')</h1>
                    <p class="text-[11px] text-slate-400 hidden sm:block">@yield('page-subtitle', '')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @hasSection('header-actions')
                    @yield('header-actions')
                @endif
                <div class="hidden sm:flex items-center gap-2 pl-3 border-l border-slate-200">
                    <span class="w-8 h-8 rounded-full brand-bg text-white grid place-items-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="leading-tight">
                        <div class="text-xs font-semibold text-slate-700">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] uppercase text-slate-400">{{ auth()->user()->counter?->name ?? 'No counter' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
