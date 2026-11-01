<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Queue') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/chart.umd.js') }}"></script>
    @stack('head')
    <style>
        :root { --brand: #1d4ed8; --brand-dark: #1e3a8a; --brand-soft: #eff6ff; }
        .brand-bg { background: var(--brand) !important; }
        .brand-text { color: var(--brand); }
        .brand-soft { background: var(--brand-soft); }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius: .9rem; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:.6rem; padding:.6rem 1.1rem; font-size:.9rem; font-weight:600; transition:.15s; }
        .btn-brand { background: var(--brand); color:#fff; box-shadow:0 8px 18px -8px rgba(29,78,216,.7); }
        .btn-brand:hover { background: var(--brand-dark); }
        .btn-ghost { background:#f3f4f6; color:#374151; } .btn-ghost:hover { background:#e5e7eb; }
        .btn-outline { background:#fff; color: var(--brand); border:1px solid #bfdbfe; }
        .btn-outline:hover { background: var(--brand-soft); }
        .nav-link { display:block; padding:.5rem .8rem; border-radius:.6rem; font-size:.9rem; color:#475569; font-weight:500; }
        .nav-link:hover { background:#f1f5f9; color:#0f172a; }
        .nav-link-active { background: var(--brand-soft); color: var(--brand); font-weight:600; }
        .badge { display:inline-block; padding:.15rem .55rem; border-radius:999px; font-size:.7rem; font-weight:700; }
        .b-waiting { background:#fef3c7; color:#92400e; }
        .b-called { background:#dbeafe; color:#1e40af; }
        .b-serving { background:#d1fae5; color:#065f46; }
        .b-done { background:#e0e7ff; color:#3730a3; }
        .b-skipped, .b-no_show, .b-cancelled { background:#fee2e2; color:#991b1b; }
        .tbl { width:100%; border-collapse:collapse; font-size:.875rem; }
        .tbl th { text-align:left; padding:.7rem .8rem; background:#f8fafc; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; }
        .tbl td { padding:.7rem .8rem; border-bottom:1px solid #f1f5f9; color:#334155; }
        .inp { width:100%; border:1px solid #d1d5db; border-radius:.6rem; padding:.6rem .85rem; font-size:.9rem; background:#fff; }
        .inp:focus { outline:none; border-color: var(--brand); box-shadow:0 0 0 3px rgba(29,78,216,.15); }
        .lbl { display:block; font-size:.8rem; font-weight:600; color:#475569; margin-bottom:.35rem; }
        .step { display:flex; align-items:center; gap:.5rem; }
        .step-dot { width:2rem; height:2rem; border-radius:999px; display:grid; place-items:center; font-size:.8rem; font-weight:700; background:#e2e8f0; color:#64748b; }
        .step-done .step-dot { background:#059669; color:#fff; }
        .step-active .step-dot { background: var(--brand); color:#fff; box-shadow:0 0 0 5px rgba(29,78,216,.15); }
        .step-line { flex:1; height:3px; background:#e2e8f0; border-radius:999px; }
        .step-done + .step-line, .line-done { background:#059669; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
<div class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 print:hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl brand-bg text-white grid place-items-center font-bold">Q</span>
                <span class="font-semibold text-slate-800">Smart Queue</span>
            </a>

            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
                <a href="{{ route('queue.place') }}" class="nav-link {{ request()->routeIs('queue.*') ? 'nav-link-active' : '' }}">Book Token</a>
                <a href="{{ route('user.history') }}" class="nav-link {{ request()->routeIs('user.history') ? 'nav-link-active' : '' }}">History</a>
                <a href="{{ route('user.notifications') }}" class="nav-link {{ request()->routeIs('user.notifications') ? 'nav-link-active' : '' }}">Notifications</a>
                <a href="{{ route('help') }}" class="nav-link {{ request()->routeIs('help') ? 'nav-link-active' : '' }}">Help</a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'nav-link-active' : '' }}">Contact</a>
            </nav>

            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="btn btn-ghost !py-1.5 !px-3 !text-xs">Log out</button>
                </form>
                <button type="button" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')"
                        class="md:hidden text-slate-500 text-xl">☰</button>
            </div>
        </div>

        <div id="mobile-nav" class="hidden md:hidden border-t border-slate-100 px-4 py-2 space-y-1">
            <a href="{{ route('user.dashboard') }}" class="nav-link">Dashboard</a>
            <a href="{{ route('queue.place') }}" class="nav-link">Book Token</a>
            <a href="{{ route('user.history') }}" class="nav-link">History</a>
            <a href="{{ route('user.notifications') }}" class="nav-link">Notifications</a>
            <a href="{{ route('help') }}" class="nav-link">Help</a>
            <a href="{{ route('contact') }}" class="nav-link">Contact</a>
            <a href="{{ route('profile.edit') }}" class="nav-link">Settings</a>
        </div>
    </header>

    <main class="flex-1">
        @if (session('success'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-12 print:hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-500">
            <span>© {{ date('Y') }} Smart Queue — Skip the wait, just check in.</span>
            <div class="flex gap-4">
                <a href="{{ route('help') }}" class="hover:text-slate-800">Help Center</a>
                <a href="{{ route('contact') }}" class="hover:text-slate-800">Contact Us</a>
                <a href="{{ route('profile.edit') }}" class="hover:text-slate-800">Settings</a>
            </div>
        </div>
    </footer>
</div>
@stack('scripts')
</body>
</html>
