<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Place;
use App\Models\QueueToken;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $placeId = request('place');
        $today = now()->toDateString();

        $tokens = QueueToken::query()
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->where('date', $today);

        $stats = [
            'total' => (clone $tokens)->count(),
            'waiting' => (clone $tokens)->where('status', QueueToken::STATUS_WAITING)->count(),
            'called' => (clone $tokens)->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])->count(),
            'done' => (clone $tokens)->where('status', QueueToken::STATUS_DONE)->count(),
            'no_show' => (clone $tokens)->whereIn('status', [QueueToken::STATUS_NO_SHOW, QueueToken::STATUS_SKIPPED])->count(),
        ];

        // Tokens per hour (line chart data)
        $hourly = $this->hourlySeries($placeId);

        // Status distribution (donut chart data)
        $statusChart = [
            'Waiting' => $stats['waiting'],
            'In Service' => $stats['called'],
            'Completed' => $stats['done'],
            'Missed' => $stats['no_show'],
        ];

        // Recent tokens
        $recent = QueueToken::with(['user', 'service', 'counter'])
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->where('date', $today)
            ->latest('id')
            ->limit(8)
            ->get();

        // Counter load
        $counterLoad = Counter::withCount(['queueTokens as today_count' => fn ($q) => $q->where('date', $today)])
            ->with(['staff', 'service'])
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->get();

        // Last 7 days volume (bar chart)
        $weekly = $this->weeklySeries($placeId);

        return view('admin.dashboard', [
            'stats' => $stats,
            'hourly' => $hourly,
            'statusChart' => $statusChart,
            'recent' => $recent,
            'counterLoad' => $counterLoad,
            'weekly' => $weekly,
            'places' => Place::orderBy('name')->get(),
            'activePlace' => $placeId,
        ]);
    }

    public function analytics()
    {
        $placeId = request('place');
        $today = now()->toDateString();

        $byService = Service::withCount(['queueTokens as total' => fn ($q) => $q->where('date', $today)])
            ->withCount(['queueTokens as done_count' => fn ($q) => $q->where('date', $today)->where('status', QueueToken::STATUS_DONE)])
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->orderByDesc('total')
            ->get();

        $byCounter = Counter::with(['staff', 'service'])
            ->withCount(['queueTokens as total' => fn ($q) => $q->where('date', $today)])
            ->withCount(['queueTokens as done_count' => fn ($q) => $q->where('date', $today)->where('status', QueueToken::STATUS_DONE)])
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->get();

        $weekly = $this->weeklySeries($placeId);
        $hourly = $this->hourlySeries($placeId);

        $avgWait = QueueToken::query()
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->whereNotNull('called_at')
            ->where('issued_at', '>=', now()->subDays(7))
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, issued_at, called_at)) as avg_wait')
            ->value('avg_wait');

        $avgService = QueueToken::query()
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->whereNotNull('completed_at')
            ->whereNotNull('called_at')
            ->where('called_at', '>=', now()->subDays(7))
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, called_at, completed_at)) as avg_service')
            ->value('avg_service');

        return view('admin.analytics', [
            'byService' => $byService,
            'byCounter' => $byCounter,
            'weekly' => $weekly,
            'hourly' => $hourly,
            'avgWait' => round($avgWait ?? 0),
            'avgService' => round($avgService ?? 0),
            'places' => Place::orderBy('name')->get(),
            'activePlace' => $placeId,
        ]);
    }

    private function hourlySeries($placeId): array
    {
        $rows = QueueToken::query()
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
            ->where('date', now()->toDateString())
            ->selectRaw('HOUR(issued_at) as h, COUNT(*) as c')
            ->groupBy('h')
            ->orderBy('h')
            ->pluck('c', 'h');

        $labels = [];
        $data = [];
        for ($h = 8; $h <= 18; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $data[] = (int) ($rows[$h] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function weeklySeries($placeId): array
    {
        $rows = QueueToken::query()
            ->when($placeId, fn ($q) => $q->where('place_id', $placeId))
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

        return ['labels' => $labels, 'data' => $data];
    }
}
