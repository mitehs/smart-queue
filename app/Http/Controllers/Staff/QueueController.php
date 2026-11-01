<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QueueToken;
use App\Services\TokenGenerator;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(private TokenGenerator $tokens)
    {
    }

    /** Waiting list for my counter. */
    public function index(Request $request)
    {
        $counter = auth()->user()->counter;

        if (! $counter) {
            return view('staff.no-counter');
        }

        $today = now()->toDateString();

        $waiting = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('issued_at')
            ->get()
            ->each(function ($t) {
                $t->people_ahead = $this->tokens->peopleAhead($t);
            });

        $all = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('staff.queue.index', compact('counter', 'waiting', 'all'));
    }

    /** Full today list with stats cards. */
    public function today(Request $request)
    {
        $counter = auth()->user()->counter;

        if (! $counter) {
            return view('staff.no-counter');
        }

        $today = now()->toDateString();

        $stats = [
            'waiting' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->where('status', QueueToken::STATUS_WAITING)->count(),
            'done' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->where('status', QueueToken::STATUS_DONE)->count(),
            'missed' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->whereIn('status', [QueueToken::STATUS_SKIPPED, QueueToken::STATUS_NO_SHOW])->count(),
            'total' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->count(),
        ];

        $tokens = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('staff.queue.today', compact('counter', 'stats', 'tokens'));
    }

    /** Next 5 in line. */
    public function upcoming()
    {
        $counter = auth()->user()->counter;

        if (! $counter) {
            return view('staff.no-counter');
        }

        $upcoming = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', now()->toDateString())
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('issued_at')
            ->limit(10)
            ->get();

        return view('staff.queue.upcoming', compact('counter', 'upcoming'));
    }

    /** Served / skipped history. */
    public function history(Request $request)
    {
        $counter = auth()->user()->counter;

        if (! $counter) {
            return view('staff.no-counter');
        }

        $tokens = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->whereIn('status', [QueueToken::STATUS_DONE, QueueToken::STATUS_SKIPPED, QueueToken::STATUS_NO_SHOW])
            ->when($request->date, fn ($q) => $q->where('date', $request->date))
            ->latest('completed_at')
            ->paginate(20)
            ->withQueryString();

        return view('staff.queue.history', compact('counter', 'tokens'));
    }
}
