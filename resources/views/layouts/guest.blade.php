<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: figtree, ui-sans-serif, system-ui, sans-serif; background:#f8fafc; color:#0f172a; }
            .g-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; box-shadow:0 10px 30px -20px rgba(15,23,42,.4); }
            .g-btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; background:#1d4ed8; color:#fff; font-weight:600;
                     padding:.65rem 1.3rem; border-radius:.7rem; font-size:.95rem; transition:.15s; box-shadow:0 10px 22px -12px rgba(29,78,216,.9); }
            .g-btn:hover { background:#1e3a8a; }
            .g-link { color:#1d4ed8; font-weight:600; }
            .g-link:hover { text-decoration:underline; }
        </style>
    </head>
    <body class="min-h-screen flex flex-col sm:justify-center items-center py-10 px-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-6">
            <span class="w-10 h-10 rounded-xl bg-blue-700 text-white grid place-items-center font-bold text-lg">Q</span>
            <span class="text-lg font-semibold text-slate-800">Smart Queue</span>
        </a>

        <div class="w-full max-w-md g-card p-6 sm:p-8">
            {{ $slot }}
        </div>

        <a href="{{ route('home') }}" class="mt-5 text-sm text-slate-500 hover:text-slate-800">← Back to home</a>
    </body>
</html>
