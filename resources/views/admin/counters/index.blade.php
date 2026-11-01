@extends('layouts.admin')

@section('title', 'Counters')
@section('page-title', 'Counters')
@section('page-subtitle', 'Windows and desks that serve customers')
@section('active', 'counters')

@section('content')
    {{-- Filter bar --}}
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-52">
                <label class="lbl">Place</label>
                <select name="place" class="inp">
                    <option value="">All places</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" @selected($activePlace == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Filter</button>
                <a href="{{ route('admin.counters.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    {{-- Add counter --}}
    <div class="card p-5 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4">Add Counter</h3>
        <form method="POST" action="{{ route('admin.counters.store') }}">
            @csrf
            <div class="grid sm:grid-cols-2 xl:grid-cols-5 gap-4">
                <div>
                    <label class="lbl">Place</label>
                    <select name="place_id" class="inp">
                        <option value="">Select a place</option>
                        @foreach ($places as $place)
                            <option value="{{ $place->id }}" @selected(old('place_id', request('place')) == $place->id)>{{ $place->name }}</option>
                        @endforeach
                    </select>
                    @error('place_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Service</label>
                    <select name="service_id" class="inp">
                        <option value="">Select a service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Staff</label>
                    <select name="staff_id" class="inp">
                        <option value="">— Unassigned —</option>
                        @foreach ($staffList as $member)
                            <option value="{{ $member->id }}" @selected(old('staff_id') == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('staff_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="inp" placeholder="e.g. Window 1">
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="inp" placeholder="e.g. Ground floor, left wing">
                    @error('location')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-brand">＋ Add Counter</button>
            </div>
        </form>
    </div>

    {{-- Counters table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">All counters</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Counter</th>
                        <th>Place</th>
                        <th>Service</th>
                        <th>Staff</th>
                        <th>Waiting</th>
                        <th>Today</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($counters as $counter)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $counter->name }}</div>
                                @if ($counter->location)
                                    <div class="text-[11px] text-slate-400">{{ $counter->location }}</div>
                                @endif
                            </td>
                            <td>{{ $counter->place?->name ?? '—' }}</td>
                            <td>{{ $counter->service?->name ?? '—' }}</td>
                            <td>
                                @if ($counter->staff)
                                    {{ $counter->staff->name }}
                                @else
                                    <span class="text-slate-400">Unassigned</span>
                                @endif
                            </td>
                            <td><span class="badge b-waiting">{{ $counter->waiting_count }}</span></td>
                            <td>{{ $counter->today_count }}</td>
                            <td>
                                @if ($counter->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                <details class="inline-block text-left align-top">
                                    <summary class="text-xs font-semibold brand-text cursor-pointer select-none">Edit</summary>
                                    <form method="POST" action="{{ route('admin.counters.update', $counter) }}" class="mt-2 p-3 rounded-lg bg-slate-50 space-y-2 w-64">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="lbl">Name</label>
                                            <input type="text" name="name" value="{{ old('name', $counter->name) }}" class="inp">
                                            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="lbl">Location</label>
                                            <input type="text" name="location" value="{{ old('location', $counter->location) }}" class="inp">
                                        </div>
                                        <div>
                                            <label class="lbl">Service</label>
                                            <select name="service_id" class="inp">
                                                <option value="">Select a service</option>
                                                @foreach ($services as $service)
                                                    <option value="{{ $service->id }}" @selected(old('service_id', $counter->service_id) == $service->id)>{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="lbl">Staff</label>
                                            <select name="staff_id" class="inp">
                                                <option value="">— Unassigned —</option>
                                                @foreach ($staffList as $member)
                                                    <option value="{{ $member->id }}" @selected(old('staff_id', $counter->staff_id) == $member->id)>{{ $member->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center justify-between pt-1">
                                            <button class="btn btn-brand !py-1 !text-xs">Save</button>
                                            <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                                                <input type="checkbox" name="is_active" value="1" class="w-3.5 h-3.5 rounded border-slate-300" @checked(old('is_active', $counter->is_active))>
                                                Active
                                            </label>
                                        </div>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.counters.destroy', $counter) }}" class="inline-block ml-3 align-top" onsubmit="return confirm('Delete this counter?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger !py-1 !text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-slate-400 py-6">No counters yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $counters->links() }}
    </div>
@endsection
