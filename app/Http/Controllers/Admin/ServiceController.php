<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::with(['place', 'counters'])
            ->withCount('queueTokens')
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'places' => Place::orderBy('name')->get(),
            'activePlace' => $request->place,
            'q' => $request->q,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:10|alpha_dash',
            'description' => 'nullable|string|max:500',
            'est_minutes' => 'required|integer|min:1|max:240',
        ]);

        Service::create($data + ['is_active' => true]);

        return redirect()->route('admin.services.index')->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:10|alpha_dash',
            'description' => 'nullable|string|max:500',
            'est_minutes' => 'required|integer|min:1|max:240',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->queueTokens()->exists()) {
            return back()->with('error', 'Cannot delete: tokens already issued for this service. Deactivate it instead.');
        }

        $service->counters()->delete();
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }
}
