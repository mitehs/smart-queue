@extends('layouts.admin')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff')
@section('page-subtitle', 'Update staff details and counter assignment')
@section('active', 'staff.index')

@section('content')
    @php $servedCount = $staff->queueTokens()->where('status', 'done')->count(); @endphp

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.staff.index') }}" class="text-xs font-semibold brand-text hover:underline">← Back to list</a>

        <div class="card p-4 mt-3 mb-4 text-center">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Served Tokens</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $servedCount }}</div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-slate-800">{{ $staff->name }}</h3>
                    <p class="text-xs text-slate-400">{{ $staff->email }}</p>
                </div>
                @if ($staff->is_active)
                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                @else
                    <span class="badge bg-red-100 text-red-700">Inactive</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.staff.update', $staff) }}">
                @csrf
                @method('PUT')
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="lbl">Name</label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="inp" placeholder="e.g. John Carter">
                        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Email</label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="inp" placeholder="john@example.com">
                        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="inp" placeholder="+1 555 010 1234">
                        @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">New Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="inp" placeholder="Min. 8 characters">
                        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="lbl">New Password Confirmation</label>
                        <input type="password" name="password_confirmation" class="inp" placeholder="Repeat password">
                    </div>
                    <div>
                        <label class="lbl">Place <span class="text-slate-400 font-normal">(optional)</span></label>
                        <select name="place_id" class="inp">
                            <option value="">— None —</option>
                            @foreach ($places as $place)
                                <option value="{{ $place->id }}" @selected(old('place_id', $staff->place_id) == $place->id)>{{ $place->name }}</option>
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
                                        <option value="{{ $counter->id }}" @selected(old('counter_id', $staff->counter?->id) == $counter->id)>
                                            {{ $counter->name }} — {{ $counter->service?->name ?? 'No service' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('counter_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-slate-300" @checked(old('is_active', $staff->is_active))>
                            Active account (can log in and serve customers)
                        </label>
                        @error('is_active')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button class="btn btn-brand">Save changes</button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
