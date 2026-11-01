<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\QueueToken;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function index(Request $request)
    {
        $tokens = QueueToken::with(['user', 'service', 'counter', 'place'])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->when($request->service, fn ($q) => $q->where('service_id', $request->service))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->date, fn ($q) => $q->where('date', $request->date))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('token_no', 'like', "%{$term}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"))))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $services = \App\Models\Service::orderBy('name')->get();

        return view('admin.tokens.index', [
            'tokens' => $tokens,
            'places' => Place::orderBy('name')->get(),
            'services' => $services,
            'filters' => $request->only(['place', 'service', 'status', 'date', 'q']),
        ]);
    }

    public function show(QueueToken $token)
    {
        $token->load(['user', 'service', 'counter.staff', 'place', 'servedBy']);

        $peopleAhead = QueueToken::where('service_id', $token->service_id)
            ->where('date', $token->date)
            ->where('status', QueueToken::STATUS_WAITING)
            ->where('issued_at', '<', $token->issued_at)
            ->count();

        return view('admin.tokens.show', compact('token', 'peopleAhead'));
    }

    public function update(Request $request, QueueToken $token)
    {
        $data = $request->validate([
            'status' => 'required|in:waiting,called,serving,done,skipped,no_show,cancelled',
            'counter_id' => 'nullable|exists:counters,id',
        ]);

        if (in_array($data['status'], ['called', 'serving'], true)) {
            $token->update([
                'status' => $data['status'],
                'counter_id' => $data['counter_id'] ?? $token->counter_id,
                'called_at' => $token->called_at ?? now(),
            ]);
        } elseif (in_array($data['status'], ['done', 'skipped', 'no_show'], true)) {
            $token->update([
                'status' => $data['status'],
                'completed_at' => now(),
                'served_by' => $token->served_by ?? auth()->id(),
            ]);
        } else {
            $token->update(['status' => $data['status'], 'counter_id' => $data['counter_id'] ?? $token->counter_id]);
        }

        return back()->with('success', "Token {$token->token_no} marked as {$data['status']}.");
    }

    public function destroy(QueueToken $token)
    {
        $token->delete();

        return back()->with('success', "Token {$token->token_no} deleted.");
    }
}
