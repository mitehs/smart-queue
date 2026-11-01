<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\QueueToken;
use App\Models\Service;
use App\Services\TokenGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function __construct(private TokenGenerator $tokenGenerator)
    {
    }

    /** Step 1: choose the place (office / bank). */
    public function place()
    {
        $places = Place::where('is_active', true)
            ->withCount(['services' => fn ($q) => $q->where('is_active', true)])
            ->get();

        return view('user.queue.place', compact('places'));
    }

    /** Step 2: choose the service inside a place. */
    public function service(Request $request)
    {
        $place = $this->findPlace($request);

        $services = Service::where('place_id', $place->id)
            ->where('is_active', true)
            ->withCount(['counters as free_counters' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('user.queue.service', compact('place', 'services'));
    }

    /** Step 3: choose a counter for that service. */
    public function counter(Request $request)
    {
        $place = $this->findPlace($request);
        $service = Service::where('place_id', $place->id)->where('is_active', true)->findOrFail($request->service_id);

        $today = now()->toDateString();

        $counters = DB::table('counters')
            ->where('counters.place_id', $place->id)
            ->where('counters.service_id', $service->id)
            ->where('counters.is_active', true)
            ->leftJoin('users as staff', 'staff.id', '=', 'counters.staff_id')
            ->leftJoin('queue_tokens as qt', function ($join) use ($today) {
                $join->on('qt.counter_id', '=', 'counters.id')
                    ->where('qt.date', $today)
                    ->where('qt.status', 'waiting');
            })
            ->select(
                'counters.id',
                'counters.name',
                'counters.location',
                'staff.name as staff_name',
                DB::raw('COUNT(qt.id) as waiting_count')
            )
            ->groupBy('counters.id', 'counters.name', 'counters.location', 'staff.name')
            ->orderBy('counters.name')
            ->get()
            ->map(function ($c) use ($service) {
                $c->est_wait = $c->waiting_count * $service->est_minutes;

                return $c;
            });

        return view('user.queue.counter', compact('place', 'service', 'counters'));
    }

    /** Step 4: issue the token. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'service_id' => 'required|exists:services,id',
            'counter_id' => 'required|exists:counters,id',
        ]);

        $user = auth()->user();
        $service = Service::findOrFail($data['service_id']);
        $counter = \App\Models\Counter::findOrFail($data['counter_id']);

        abort_if($counter->service_id !== $service->id, 422, 'Selected counter does not offer this service.');

        // One open token per user per service per day
        $existing = QueueToken::where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->where('date', now()->toDateString())
            ->whereIn('status', [QueueToken::STATUS_WAITING, QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->first();

        if ($existing) {
            return redirect()->route('queue.active')->with('error', 'You already have an active token ('.$existing->token_no.') for this service today.');
        }

        $token = DB::transaction(function () use ($data, $user, $service, $counter) {
            $tokenNo = $this->tokenGenerator->next($service);

            return QueueToken::create([
                'token_no' => $tokenNo,
                'user_id' => $user->id,
                'place_id' => $data['place_id'],
                'service_id' => $service->id,
                'counter_id' => $counter->id,
                'status' => QueueToken::STATUS_WAITING,
                'issued_at' => now(),
                'date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('queue.success', $token);
    }

    /** Success screen after booking. */
    public function success(\App\Models\QueueToken $token)
    {
        abort_unless($token->user_id === auth()->id(), 403);

        $token->load(['service', 'counter', 'place']);

        return view('user.queue.success', [
            'token' => $token,
            'peopleAhead' => $this->tokenGenerator->peopleAhead($token),
        ]);
    }

    /** Live token status screen. */
    public function active()
    {
        $token = QueueToken::with(['service', 'counter', 'place'])
            ->where('user_id', auth()->id())
            ->whereIn('status', [QueueToken::STATUS_WAITING, QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('id')
            ->first();

        if (! $token) {
            return redirect()->route('queue.place')->with('error', 'You have no active token. Please book one.');
        }

        return view('user.queue.active', [
            'token' => $token,
            'peopleAhead' => $this->tokenGenerator->peopleAhead($token),
        ]);
    }

    /** Progress timeline screen. */
    public function progress(\App\Models\QueueToken $token)
    {
        abort_unless($token->user_id === auth()->id(), 403);

        $token->load(['service', 'counter', 'place']);

        return view('user.queue.progress', [
            'token' => $token,
            'peopleAhead' => $this->tokenGenerator->peopleAhead($token),
        ]);
    }

    /** Full ticket detail. */
    public function ticket(\App\Models\QueueToken $token)
    {
        abort_unless($token->user_id === auth()->id(), 403);

        $token->load(['service', 'counter.staff', 'place']);

        return view('user.queue.ticket', [
            'token' => $token,
            'peopleAhead' => $this->tokenGenerator->peopleAhead($token),
        ]);
    }

    /** Update personal details before being served. */
    public function editUpdate()
    {
        return view('user.queue.update', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|unique:users,email,'.auth()->id(),
        ]);

        auth()->user()->update($data);

        return redirect()->route('queue.update')->with('success', 'Your details updated successfully.');
    }

    /** Cancel own token. */
    public function cancel(\App\Models\QueueToken $token)
    {
        abort_unless($token->user_id === auth()->id(), 403);

        abort_unless($token->status === QueueToken::STATUS_WAITING, 422, 'Only waiting tokens can be cancelled.');

        $token->update(['status' => QueueToken::STATUS_CANCELLED, 'completed_at' => now()]);

        return redirect()->route('user.history')->with('success', 'Token cancelled.');
    }

    /** Live status JSON for auto-refresh. */
    public function status()
    {
        $token = QueueToken::with(['counter', 'service'])
            ->where('user_id', auth()->id())
            ->whereIn('status', [QueueToken::STATUS_WAITING, QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('id')
            ->first();

        if (! $token) {
            return response()->json(['ok' => false]);
        }

        return response()->json([
            'ok' => true,
            'token_no' => $token->token_no,
            'status' => $token->status,
            'counter' => $token->counter?->name,
            'people_ahead' => $this->tokenGenerator->peopleAhead($token),
        ]);
    }

    private function findPlace(Request $request): Place
    {
        return Place::where('is_active', true)->findOrFail($request->place_id);
    }
}
