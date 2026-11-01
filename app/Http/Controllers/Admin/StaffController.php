<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::with(['place', 'counter'])
            ->withCount(['queueTokens as tokens_served' => fn ($q) => $q->where('status', 'done')])
            ->where('role', User::ROLE_STAFF)
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->when($request->status !== null && $request->status !== '', fn ($q, $s) => $q->where('is_active', $s === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.staff.index', [
            'staff' => $staff,
            'places' => Place::orderBy('name')->get(),
            'activePlace' => $request->place,
            'q' => $request->q,
            'status' => $request->status,
        ]);
    }

    public function create()
    {
        return view('admin.staff.create', [
            'places' => Place::orderBy('name')->get(),
            'counters' => Counter::with(['place', 'service'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Password::min(8)],
            'place_id' => 'nullable|exists:places,id',
            'counter_id' => 'nullable|exists:counters,id',
        ]);

        $staff = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_STAFF,
            'place_id' => $data['place_id'] ?? null,
            'is_active' => true,
        ]);

        if (! empty($data['counter_id'])) {
            Counter::whereKey($data['counter_id'])->update(['staff_id' => $staff->id]);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        return view('admin.staff.edit', [
            'staff' => $staff,
            'places' => Place::orderBy('name')->get(),
            'counters' => Counter::with(['place', 'service'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,'.$staff->id,
            'phone' => 'nullable|string|max:30',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'place_id' => 'nullable|exists:places,id',
            'counter_id' => 'nullable|exists:counters,id',
            'is_active' => 'nullable|boolean',
        ]);

        $staff->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'place_id' => $data['place_id'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        if (! empty($data['password'])) {
            $staff->update(['password' => Hash::make($data['password'])]);
        }

        // Detach this staff from any counter, then attach the selected one
        if (array_key_exists('counter_id', $data)) {
            Counter::where('staff_id', $staff->id)->update(['staff_id' => null]);
            if (! empty($data['counter_id'])) {
                Counter::whereKey($data['counter_id'])->update(['staff_id' => $staff->id]);
            }
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        if ($staff->queueTokens()->exists()) {
            return back()->with('error', 'Cannot delete: this staff has served tokens. Deactivate the account instead.');
        }

        Counter::where('staff_id', $staff->id)->update(['staff_id' => null]);
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted.');
    }

    /** Staff-to-service assignment matrix. */
    public function assignments()
    {
        $places = Place::with(['counters' => fn ($q) => $q->with(['service', 'staff'])])->get();

        return view('admin.staff.assignments', compact('places'));
    }
}
