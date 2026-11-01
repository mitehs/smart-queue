@extends('layouts.admin')

@section('title', 'Services')
@section('page-title', 'Services')
@section('page-subtitle', 'Define the services customers can take tickets for')
@section('active', 'services')

@section('content')
    {{-- Filter bar --}}
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-52">
                <label class="lbl">Place</label>
                <select name="place" class="inp">
                    <option value="">All places</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" @selected(request('place') == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[12rem]">
                <label class="lbl">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="inp" placeholder="Search by name or code...">
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Filter</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    {{-- Add service --}}
    <div class="card p-5 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4">Add Service</h3>
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
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
                    <label class="lbl">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="inp" placeholder="e.g. Account Opening">
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" maxlength="10" class="inp uppercase" placeholder="e.g. AOP" oninput="this.value = this.value.toUpperCase()">
                    <p class="text-[11px] text-slate-400 mt-1">Max 10 characters, printed on the token.</p>
                    @error('code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="lbl">Est. minutes</label>
                    <input type="number" name="est_minutes" value="{{ old('est_minutes', 5) }}" min="1" class="inp" placeholder="5">
                    @error('est_minutes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2 xl:col-span-4">
                    <label class="lbl">Description</label>
                    <textarea name="description" rows="2" class="inp" placeholder="Short description shown to staff...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-brand">＋ Add Service</button>
            </div>
        </form>
    </div>

    {{-- Services table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">All services</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Place</th>
                        <th>Est. Min</th>
                        <th>Counters</th>
                        <th>Tokens</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td>
                                <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold brand-soft brand-text">
                                    {{ $service->code }}
                                </span>
                            </td>
                            <td class="font-semibold">{{ $service->name }}</td>
                            <td>{{ $service->place?->name ?? '—' }}</td>
                            <td>{{ $service->est_minutes }} min</td>
                            <td>{{ $service->counters->count() }}</td>
                            <td>{{ $service->queue_tokens_count }}</td>
                            <td>
                                @if ($service->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                <details class="inline-block text-left align-top">
                                    <summary class="text-xs font-semibold brand-text cursor-pointer select-none">Edit</summary>
                                    <form method="POST" action="{{ route('admin.services.update', $service) }}" class="mt-2 p-3 rounded-lg bg-slate-50 space-y-2 w-64">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="lbl">Name</label>
                                            <input type="text" name="name" value="{{ old('name', $service->name) }}" class="inp">
                                            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="lbl">Code</label>
                                            <input type="text" name="code" value="{{ old('code', $service->code) }}" maxlength="10" class="inp uppercase">
                                            @error('code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="lbl">Est. minutes</label>
                                            <input type="number" name="est_minutes" value="{{ old('est_minutes', $service->est_minutes) }}" min="1" class="inp">
                                        </div>
                                        <div>
                                            <label class="lbl">Description</label>
                                            <textarea name="description" rows="2" class="inp">{{ old('description', $service->description) }}</textarea>
                                        </div>
                                        <div class="flex items-center justify-between pt-1">
                                            <button class="btn btn-brand !py-1 !text-xs">Save</button>
                                            <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                                                <input type="checkbox" name="is_active" value="1" class="w-3.5 h-3.5 rounded border-slate-300" @checked(old('is_active', $service->is_active))>
                                                Active
                                            </label>
                                        </div>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline-block ml-3 align-top" onsubmit="return confirm('Delete this service?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger !py-1 !text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-slate-400 py-6">No services yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $services->links() }}
    </div>
@endsection
