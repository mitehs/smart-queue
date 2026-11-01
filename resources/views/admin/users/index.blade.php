@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')
@section('page-subtitle', 'Customers who have taken queue tokens')
@section('active', 'users')

@section('content')
    {{-- Filter bar --}}
    <div class="card p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[12rem]">
                <label class="lbl">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="inp" placeholder="Search by name, email or phone...">
            </div>
            <div class="w-full sm:w-52">
                <label class="lbl">Place</label>
                <select name="place" class="inp">
                    <option value="">All places</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" @selected(request('place') == $place->id)>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-brand">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>

    {{-- Users table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Place</th>
                        <th>Tokens</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="font-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>{{ $user->place?->name ?? '—' }}</td>
                            <td>{{ $user->queue_tokens_count }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="badge bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn !py-1 !text-xs {{ $user->is_active ? 'btn-ghost' : 'btn-success' }}">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-semibold brand-text hover:underline ml-3">View</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block ml-3" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger !py-1 !text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-slate-400 py-6">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endsection
