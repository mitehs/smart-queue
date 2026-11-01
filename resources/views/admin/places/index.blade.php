@extends('layouts.admin')

@section('title', 'Places')
@section('page-title', 'Places')
@section('page-subtitle', 'Manage branches, offices and service locations')
@section('active', 'places')

@section('content')
    {{-- Add place --}}
    <div class="card p-5 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4">Add Place</h3>
        <form method="POST" action="{{ route('admin.places.store') }}">
            @csrf
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="lbl">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="inp" placeholder="e.g. Main Branch">
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Type</label>
                    <select name="type" class="inp">
                        @foreach (['bank', 'government', 'hospital', 'office', 'service'] as $type)
                            <option value="{{ $type }}" @selected(old('type') == $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="inp" placeholder="Street, city...">
                    @error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="inp" placeholder="+1 555 010 1234">
                    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-brand">＋ Add Place</button>
            </div>
        </form>
    </div>

    {{-- Places grid --}}
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($places as $place)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-800 truncate">{{ $place->name }}</h3>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold brand-soft brand-text">
                            {{ ucfirst($place->type) }}
                        </span>
                    </div>
                    @if ($place->is_active)
                        <span class="badge bg-emerald-100 text-emerald-700 shrink-0">Active</span>
                    @else
                        <span class="badge bg-slate-200 text-slate-500 shrink-0">Inactive</span>
                    @endif
                </div>

                @if ($place->address)
                    <p class="text-sm text-slate-500 mt-3">{{ $place->address }}</p>
                @endif
                @if ($place->phone)
                    <p class="text-sm text-slate-500 mt-1">{{ $place->phone }}</p>
                @endif

                <div class="grid grid-cols-4 gap-2 mt-4 text-center">
                    <div class="rounded-lg bg-slate-50 py-2">
                        <div class="text-base font-bold text-slate-800">{{ $place->services_count }}</div>
                        <div class="text-[10px] uppercase text-slate-400">Services</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 py-2">
                        <div class="text-base font-bold text-slate-800">{{ $place->counters_count }}</div>
                        <div class="text-[10px] uppercase text-slate-400">Counters</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 py-2">
                        <div class="text-base font-bold text-slate-800">{{ $place->users_count }}</div>
                        <div class="text-[10px] uppercase text-slate-400">Users</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 py-2">
                        <div class="text-base font-bold text-slate-800">{{ $place->today_tokens_count }}</div>
                        <div class="text-[10px] uppercase text-slate-400">Today</div>
                    </div>
                </div>

                <details class="mt-4">
                    <summary class="text-xs font-semibold brand-text cursor-pointer select-none">Edit place</summary>
                    <form method="POST" action="{{ route('admin.places.update', $place) }}" class="mt-3 p-3 rounded-lg bg-slate-50 space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="lbl">Name</label>
                            <input type="text" name="name" value="{{ old('name', $place->name) }}" class="inp">
                            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="lbl">Type</label>
                            <select name="type" class="inp">
                                @foreach (['bank', 'government', 'hospital', 'office', 'service'] as $type)
                                    <option value="{{ $type }}" @selected(old('type', $place->type) == $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="lbl">Address</label>
                            <input type="text" name="address" value="{{ old('address', $place->address) }}" class="inp">
                        </div>
                        <div>
                            <label class="lbl">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $place->phone) }}" class="inp">
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="btn btn-brand !py-1.5 !text-xs">Save changes</button>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-slate-300" @checked(old('is_active', $place->is_active))>
                                Active
                            </label>
                        </div>
                    </form>
                </details>

                <form method="POST" action="{{ route('admin.places.destroy', $place) }}" class="mt-3" onsubmit="return confirm('Delete this place?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger !py-1.5 !text-xs">Delete</button>
                </form>
            </div>
        @empty
            <div class="card p-10 text-center text-slate-400 md:col-span-2 xl:col-span-3">
                No places yet. Add your first place using the form above.
            </div>
        @endforelse
    </div>
@endsection
