<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::withCount(['services', 'counters', 'users'])
            ->withCount(['queueTokens as today_tokens' => fn ($q) => $q->where('date', now()->toDateString())])
            ->latest()
            ->get();

        return view('admin.places.index', compact('places'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:bank,government,hospital,office,service',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        Place::create($data);

        return redirect()->route('admin.places.index')->with('success', 'Place added successfully.');
    }

    public function update(Request $request, Place $place)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:bank,government,hospital,office,service',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
        ]);

        $place->update($data);

        return redirect()->route('admin.places.index')->with('success', 'Place updated successfully.');
    }

    public function destroy(Place $place)
    {
        if ($place->queueTokens()->exists()) {
            return back()->with('error', 'Cannot delete: this place already has queue tokens. Deactivate it instead.');
        }

        $place->delete();

        return redirect()->route('admin.places.index')->with('success', 'Place deleted.');
    }
}
