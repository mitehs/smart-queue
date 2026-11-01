<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QueueToken;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $counter = auth()->user()->counter;

        if (! $counter) {
            return view('staff.no-counter');
        }

        $today = now()->toDateString();

        $stats = [
            'waiting' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->where('status', QueueToken::STATUS_WAITING)->count(),
            'serving' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])->count(),
            'done' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->where('status', QueueToken::STATUS_DONE)->count(),
            'missed' => QueueToken::where('counter_id', $counter->id)->where('date', $today)->whereIn('status', [QueueToken::STATUS_SKIPPED, QueueToken::STATUS_NO_SHOW])->count(),
        ];

        $current = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->first();

        $recent = QueueToken::with('user')
            ->where('counter_id', $counter->id)
            ->where('date', $today)
            ->latest('id')
            ->limit(8)
            ->get();

        // 7-day line chart
        $rows = QueueToken::where('counter_id', $counter->id)
            ->where('date', '>=', now()->subDays(6)->toDateString())
            ->selectRaw('date, COUNT(*) as c')
            ->groupBy('date')
            ->pluck('c', 'date');

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('D d');
            $data[] = (int) ($rows[$d] ?? 0);
        }

        return view('staff.dashboard', [
            'counter' => $counter,
            'stats' => $stats,
            'current' => $current,
            'recent' => $recent,
            'chart' => ['labels' => $labels, 'data' => $data],
        ]);
    }
}
