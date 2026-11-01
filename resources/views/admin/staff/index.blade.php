@extends('layouts.admin')

@section('title', 'Manage Staff')
@section('page-title', 'Manage Staff')
@section('page-subtitle', 'Staff accounts and their counter assignments')
@section('active', 'staff.index')

@section('content')
    {{-- Toolbar --}}
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
            <div class="flex-1 min-w-[12rem]">
                <label class="lbl">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="inp" placeholder="Search by name, email or phone...">
            </div>
            <div class="w-full sm:w-40">
                <label class="lbl">Status</label>
                <select name="status" class="inp">
                    <option value="">All</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Filter</button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-brand">＋ Create Staff</a>
            <a href="{{ route('admin.staff.assignments') }}" class="btn btn-ghost">⇄ Service Assignments</a>
        </div>
    </div>

    {{-- Staff table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Place</th>
                        <th>Counter</th>
                        <th>Tokens Served</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $member->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $member->email }}</div>
                            </td>
                            <td>{{ $member->phone ?? '—' }}</td>
                            <td>{{ $member->place?->name ?? '—' }}</td>
                            <td>
                                @if ($member->counter)
                                    {{ $member->counter->name }}
                                @else
                                    <span class="text-slate-400">No counter</span>
                                @endif
                            </td>
                            <td>{{ $member->tokens_served }}</td>
                            <td>
                                @if ($member->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="badge bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                <a href="{{ route('admin.staff.edit', $member) }}" class="text-xs font-semibold brand-text hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" class="inline-block ml-3" onsubmit="return confirm('Delete this staff member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger !py-1 !text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-slate-400 py-6">No staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $staff->links() }}
    </div>
@endsection
