<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QueueToken;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    private function myCounter()
    {
        return auth()->user()->counter;
    }

    /** Now-serving screen with action buttons. */
    public function active()
    {
        $counter = $this->myCounter();

        if (! $counter) {
            return view('staff.no-counter');
        }

        $today = now()->toDateString();

        $current = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->first();

        $next = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('issued_at')
            ->first();

        $waitingCount = QueueToken::where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->count();

        return view('staff.tokens.active', compact('counter', 'current', 'next', 'waitingCount'));
    }

    /** Call the next waiting token. */
    public function callNext()
    {
        $counter = $this->myCounter();

        if (! $counter) {
            return back()->with('error', 'No counter assigned.');
        }

        $today = now()->toDateString();

        // Finish whatever is currently active at this counter
        QueueToken::where('counter_id', $counter->id)
            ->where('date', $today)
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->update(['status' => QueueToken::STATUS_DONE, 'completed_at' => now(), 'served_by' => auth()->id()]);

        $next = QueueToken::where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('issued_at')
            ->first();

        if (! $next) {
            return back()->with('error', 'No one is waiting in your queue.');
        }

        $next->update([
            'status' => QueueToken::STATUS_CALLED,
            'called_at' => now(),
        ]);

        return back()->with('success', "Token {$next->token_no} called.");
    }

    /** Mark current token as served. */
    public function complete()
    {
        $counter = $this->myCounter();

        if (! $counter) {
            return back()->with('error', 'No counter assigned.');
        }

        $current = QueueToken::where('counter_id', $counter->id)
            ->where('date', now()->toDateString())
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->first();

        if (! $current) {
            return back()->with('error', 'No active token to complete.');
        }

        $current->update([
            'status' => QueueToken::STATUS_DONE,
            'completed_at' => now(),
            'served_by' => auth()->id(),
        ]);

        return back()->with('success', "Token {$current->token_no} completed.");
    }

    /** Mark current token as skipped (customer will be re-called later). */
    public function skip()
    {
        return $this->closeCurrent(QueueToken::STATUS_SKIPPED, 'skipped');
    }

    /** Mark current token as no-show. */
    public function noShow()
    {
        return $this->closeCurrent(QueueToken::STATUS_NO_SHOW, 'marked as no-show');
    }

    private function closeCurrent(string $status, string $label)
    {
        $counter = $this->myCounter();

        if (! $counter) {
            return back()->with('error', 'No counter assigned.');
        }

        $current = QueueToken::where('counter_id', $counter->id)
            ->where('date', now()->toDateString())
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->first();

        if (! $current) {
            return back()->with('error', 'No active token.');
        }

        $current->update([
            'status' => $status,
            'completed_at' => now(),
            'served_by' => auth()->id(),
        ]);

        return back()->with('success', "Token {$current->token_no} {$label}.");
    }

    /** Customer detail for the staff. */
    public function show(QueueToken $token)
    {
        $counter = $this->myCounter();

        abort_unless($counter && $token->counter_id === $counter->id, 403);

        $token->load(['user', 'service', 'counter']);

        return view('staff.tokens.show', compact('token', 'counter'));
    }

    /** Move a token from my counter back to waiting pool / reassign. */
    public function recall(QueueToken $token)
    {
        $counter = $this->myCounter();

        abort_unless($counter && $token->counter_id === $counter->id, 403);

        $token->update([
            'status' => QueueToken::STATUS_WAITING,
            'called_at' => null,
            'completed_at' => null,
            'served_by' => null,
        ]);

        return back()->with('success', "Token {$token->token_no} returned to waiting.");
    }

    /** Live polling endpoint for the active screen. */
    public function status()
    {
        $counter = $this->myCounter();

        if (! $counter) {
            return response()->json(['ok' => false]);
        }

        $today = now()->toDateString();

        $current = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->first();

        $next = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('issued_at')
            ->first();

        $waitingCount = QueueToken::where('counter_id', $counter->id)
            ->where('date', $today)
            ->where('status', QueueToken::STATUS_WAITING)
            ->count();

        return response()->json([
            'ok' => true,
            'current' => $current ? [
                'id' => $current->id,
                'token_no' => $current->token_no,
                'name' => $current->user?->name,
                'status' => $current->status,
                'called_at' => optional($current->called_at)->format('H:i:s'),
            ] : null,
            'next' => $next ? ['token_no' => $next->token_no, 'name' => $next->user?->name] : null,
            'waiting' => $waitingCount,
        ]);
    }
}
