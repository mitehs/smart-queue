@extends('layouts.admin')

@section('title', 'Create Staff')
@section('page-title', 'Create Staff')
@section('page-subtitle', 'Add a new staff account to the queue system')
@section('active', 'staff.create')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.staff.index') }}" class="text-xs font-semibold brand-text hover:underline">← Back to list</a>

        <div class="card p-6 mt-3">
            <h3 class="font-semibold text-slate-800 mb-1">New Staff Member</h3>
            <p class="text-xs text-slate-400 mb-5">Create a login account and optionally assign a counter.</p>

            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="lbl">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="inp" placeholder="e.g. John Carter">
                        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="inp" placeholder="john@example.com">
                        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="inp" placeholder="+1 555 010 1234">
                        @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Password</label>
                        <input type="password" name="password" class="inp" placeholder="Min. 8 characters">
                        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Password Confirmation</label>
                        <input type="password" name="password_confirmation" class="inp" placeholder="Repeat password">
                    </div>
                    <div>
                        <label class="lbl">Place <span class="text-slate-400 font-normal">(optional)</span></label>
                        <select name="place_id" class="inp">
                            <option value="">— None —</option>
                            @foreach ($places as $place)
                                <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>{{ $place->name }}</option>
                            @endforeach
                        </select>
                        @error('place_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Counter <span class="text-slate-400 font-normal">(optional)</span></label>
                        <select name="counter_id" class="inp">
                            <option value="">— Assign later —</option>
                            @php $groupedCounters = $counters->groupBy(fn ($c) => $c->place?->name ?? '—'); @endphp
                            @foreach ($groupedCounters as $placeName => $list)
                                <optgroup label="{{ $placeName }}">
                                    @foreach ($list as $counter)
                                        <option value="{{ $counter->id }}" @selected(old('counter_id') == $counter->id)>
                                            {{ $counter->name }} — {{ $counter->service?->name ?? 'No service' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('counter_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button class="btn btn-brand">Create Staff</button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
