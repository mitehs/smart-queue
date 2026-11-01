<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\QueueToken;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('place')
            ->withCount('queueTokens')
            ->where('role', User::ROLE_USER)
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'places' => Place::orderBy('name')->get(),
            'q' => $request->q,
        ]);
    }

    public function show(User $user)
    {
        abort_unless($user->isUser(), 404);

        $tokens = QueueToken::with(['service', 'counter'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('admin.users.show', compact('user', 'tokens'));
    }

    public function toggle(User $user)
    {
        abort_unless($user->isUser(), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->isUser(), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->queueTokens()->exists()) {
            return back()->with('error', 'Cannot delete: this user has queue history. Deactivate instead.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
