@extends('layouts.admin')

@section('title', 'Service Assignments')
@section('page-title', 'Service Assignments')
@section('page-subtitle', 'Which staff serve which services at each counter')
@section('active', 'staff.assignments')

@section('content')
    {{-- Note --}}
    <div class="card p-4 mb-6 flex flex-wrap items-center justify-between gap-3 bg-blue-50 !border-blue-100">
        <p class="text-sm text-slate-600">
            Staff are assigned to counters on the <span class="font-semibold">Counters</span> page.
            Change a counter's staff member there and it updates here automatically.
        </p>
        <a href="{{ route('admin.counters.index') }}" class="btn btn-brand">Manage counters →</a>
    </div>

    @forelse ($places as $place)
        <div class="card overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">{{ $place->name }}</h3>
                <span class="text-xs text-slate-400">{{ $place->counters->count() }} counters</span>
            </div>
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Counter</th>
                            <th>Service</th>
                            <th>Assigned Staff</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($place->counters as $counter)
                            <tr>
                                <td class="font-semibold">{{ $counter->name }}</td>
                                <td>{{ $counter->service?->name ?? '—' }}</td>
                                <td>
                                    @if ($counter->staff)
                                        {{ $counter->staff->name }}
                                    @else
                                        <span class="text-slate-400">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($counter->is_active)
                                        <span class="badge bg-emerald-100 text-emerald-700">Active</span>
                                    @else
                                        <span class="badge bg-slate-200 text-slate-500">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-400 py-6">No counters in this place yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card p-10 text-center text-slate-400">
            No places yet. Add a place first, then create its counters.
        </div>
    @endforelse
@endsection
