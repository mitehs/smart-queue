@extends('layouts.admin')

@section('title', 'General Settings')
@section('page-title', 'General Settings')
@section('page-subtitle', 'App-wide options')
@section('active', 'settings.general')

@section('content')
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- General form --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-1">General</h3>
            <p class="text-xs text-slate-400 mb-4">Basic options shared across the whole app.</p>

            <form method="POST" action="{{ route('admin.settings.general.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="lbl">Token Prefix</label>
                        <input type="text" name="token_prefix" value="{{ old('token_prefix', $settings['token_prefix'] ?? '') }}" class="inp" placeholder="e.g. SQ">
                        <p class="text-[11px] text-slate-400 mt-1">Extra prefix added before every token number.</p>
                        @error('token_prefix')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="show_public_display" value="1" class="w-4 h-4 rounded border-slate-300" @checked(old('show_public_display', $settings['show_public_display'] ?? true))>
                            Show public display screen
                        </label>
                        @error('show_public_display')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-5">
                    <button class="btn btn-brand">Save settings</button>
                </div>
            </form>
        </div>

        {{-- Info card --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-1">About</h3>
            <p class="text-xs text-slate-400 mb-4">Application information.</p>

            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                    <span class="text-slate-500">App Name</span>
                    <span class="font-semibold text-slate-800">{{ config('app.name') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                    <span class="text-slate-500">Environment</span>
                    <span class="font-semibold text-slate-800">{{ ucfirst(app()->environment()) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                    <span class="text-slate-500">Laravel Version</span>
                    <span class="font-semibold text-slate-800">{{ app()->version() }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.settings.business') }}" class="text-xs font-semibold brand-text hover:underline">
                    → Configure working hours &amp; queue rules
                </a>
            </div>
        </div>
    </div>
@endsection
