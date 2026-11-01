<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Place;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $counters = Counter::with(['place', 'service', 'staff'])
            ->withCount(['queueTokens as waiting_count' => fn ($q) => $q->where('date', $today)->where('status', 'waiting')])
            ->withCount(['queueTokens as today_count' => fn ($q) => $q->where('date', $today)])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.counters.index', [
            'counters' => $counters,
            'places' => Place::orderBy('name')->get(),
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'staffList' => User::where('role', User::ROLE_STAFF)->where('is_active', true)->orderBy('name')->get(),
            'activePlace' => $request->place,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:60',
            'location' => 'nullable|string|max:120',
        ]);

        Counter::create($data + ['is_active' => true]);

        return redirect()->route('admin.counters.index')->with('success', 'Counter added successfully.');
    }

    public function update(Request $request, Counter $counter)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:60',
            'location' => 'nullable|string|max:120',
            'is_active' => 'nullable|boolean',
        ]);

        $counter->update($data);

        return redirect()->route('admin.counters.index')->with('success', 'Counter updated successfully.');
    }

    public function destroy(Counter $counter)
    {
        if ($counter->queueTokens()->exists()) {
            return back()->with('error', 'Cannot delete: tokens are linked to this counter. Deactivate it instead.');
        }

        $counter->delete();

        return redirect()->route('admin.counters.index')->with('success', 'Counter deleted.');
    }
}
