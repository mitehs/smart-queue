@extends('layouts.admin')

@section('title', 'Business Settings')
@section('page-title', 'Business Settings')
@section('page-subtitle', 'Working hours and queue behaviour')
@section('active', 'settings.business')

@section('content')
    <form method="POST" action="{{ route('admin.settings.business.update') }}">
        @csrf

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            {{-- Working hours --}}
            <div class="card p-5">
                <h3 class="font-semibold text-slate-800 mb-1">Working Hours</h3>
                <p class="text-xs text-slate-400 mb-4">When the queue is open and the daily break window.</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="lbl">Opens At</label>
                        <input type="time" name="business_open" value="{{ old('business_open', $settings['business_open'] ?? '09:00') }}" class="inp">
                        @error('business_open')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Closes At</label>
                        <input type="time" name="business_close" value="{{ old('business_close', $settings['business_close'] ?? '17:00') }}" class="inp">
                        @error('business_close')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Break Start <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="time" name="break_start" value="{{ old('break_start', $settings['break_start'] ?? '') }}" class="inp">
                        @error('break_start')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Break End <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="time" name="break_end" value="{{ old('break_end', $settings['break_end'] ?? '') }}" class="inp">
                        @error('break_end')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Queue rules --}}
            <div class="card p-5">
                <h3 class="font-semibold text-slate-800 mb-1">Queue Rules</h3>
                <p class="text-xs text-slate-400 mb-4">How the queue behaves during the day.</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="lbl">Auto-Skip After (minutes)</label>
                        <input type="number" name="auto_skip_minutes" min="1" value="{{ old('auto_skip_minutes', $settings['auto_skip_minutes'] ?? 10) }}" class="inp">
                        <p class="text-[11px] text-slate-400 mt-1">Skip a customer who doesn't show up in time.</p>
                        @error('auto_skip_minutes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Daily Token Limit</label>
                        <input type="number" name="daily_token_limit" min="1" value="{{ old('daily_token_limit', $settings['daily_token_limit'] ?? 200) }}" class="inp">
                        <p class="text-[11px] text-slate-400 mt-1">Maximum tokens per day, per place.</p>
                        @error('daily_token_limit')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Notify Before Turn</label>
                        <input type="number" name="notify_before_turn" min="0" value="{{ old('notify_before_turn', $settings['notify_before_turn'] ?? 2) }}" class="inp">
                        <p class="text-[11px] text-slate-400 mt-1">Warn customers N tokens before theirs.</p>
                        @error('notify_before_turn')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="show_public_display" value="1" class="w-4 h-4 rounded border-slate-300" @checked(old('show_public_display', $settings['show_public_display'] ?? true))>
                            Show public display screen
                        </label>
                    </div>
                    @error('show_public_display')<p class="text-red-600 text-xs mt-1 sm:col-span-2">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <button class="btn btn-brand">Save settings</button>
    </form>
@endsection
