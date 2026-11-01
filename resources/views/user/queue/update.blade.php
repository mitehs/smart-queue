@extends('layouts.user')

@section('title', 'Update Information')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Update Information</h1>
            <p class="text-sm text-slate-500">Keep your details up to date so the counter can reach you.</p>
        </div>

        <div class="card p-5 sm:p-6 mb-6">
            <form method="POST" action="{{ route('queue.update.save') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label class="lbl" for="name">Full name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="inp" required>
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="lbl" for="phone">Phone number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +1 555 123 4567" class="inp" required>
                        @error('phone')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="lbl" for="email">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="inp" required>
                        @error('email')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn btn-brand">Save Changes</button>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="card p-5 flex items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-slate-800">Change password</h3>
                <p class="text-sm text-slate-500 mt-0.5">Update your account password from the profile settings page.</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline shrink-0">Go to Settings</a>
        </div>
    </div>
@endsection
