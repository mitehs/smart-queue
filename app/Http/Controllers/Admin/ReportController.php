<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Place;
use App\Models\QueueToken;
use App\Models\Service;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $base = QueueToken::query()
            ->whereBetween('date', [$from, $to])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place));

        $stats = [
            'total' => (clone $base)->count(),
            'done' => (clone $base)->where('status', QueueToken::STATUS_DONE)->count(),
            'waiting' => (clone $base)->where('status', QueueToken::STATUS_WAITING)->count(),
            'missed' => (clone $base)->whereIn('status', [QueueToken::STATUS_NO_SHOW, QueueToken::STATUS_SKIPPED])->count(),
            'cancelled' => (clone $base)->where('status', QueueToken::STATUS_CANCELLED)->count(),
        ];

        $stats['completion'] = $stats['total'] > 0
            ? round($stats['done'] / $stats['total'] * 100)
            : 0;

        // Daily volume (line chart)
        $dailyRows = (clone $base)
            ->selectRaw('date, COUNT(*) as c')
            ->groupBy('date')
            ->pluck('c', 'date');

        $labels = [];
        $data = [];
        $cursor = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('d M');
            $data[] = (int) ($dailyRows[$cursor->toDateString()] ?? 0);
            $cursor->addDay();
        }

        // Peak hours (bar chart)
        $hourRows = (clone $base)
            ->selectRaw('HOUR(issued_at) as h, COUNT(*) as c')
            ->groupBy('h')
            ->orderBy('h')
            ->pluck('c', 'h');

        $hourLabels = [];
        $hourData = [];
        for ($h = 8; $h <= 18; $h++) {
            $hourLabels[] = sprintf('%02d:00', $h);
            $hourData[] = (int) ($hourRows[$h] ?? 0);
        }

        $table = (clone $base)->with(['user', 'service', 'counter'])->latest('id')->limit(200)->get();

        return view('admin.reports.index', [
            'stats' => $stats,
            'chart' => ['labels' => $labels, 'data' => $data],
            'peak' => ['labels' => $hourLabels, 'data' => $hourData],
            'table' => $table,
            'places' => Place::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function analytics(Request $request)
    {
        [$from, $to] = $this->range($request);

        $base = fn () => QueueToken::query()
            ->whereBetween('date', [$from, $to])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place));

        // Donut: share by service
        $byService = Service::withCount(['queueTokens as total' => fn ($q) => $q->whereBetween('date', [$from, $to])])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->having('total', '>', 0)
            ->get();

        // Bars: per counter performance
        $byCounter = Counter::with(['service', 'staff'])
            ->withCount(['queueTokens as total' => fn ($q) => $q->whereBetween('date', [$from, $to])])
            ->withCount(['queueTokens as done_count' => fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', QueueToken::STATUS_DONE)])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->get();

        $avgWait = ($base())->whereNotNull('called_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, issued_at, called_at)) as v')
            ->value('v');

        $avgServiceTime = ($base())->whereNotNull('completed_at')->whereNotNull('called_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, called_at, completed_at)) as v')
            ->value('v');

        $dailyRows = ($base())->selectRaw('date, COUNT(*) as c')->groupBy('date')->pluck('c', 'date');

        $labels = [];
        $data = [];
        $cursor = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('d M');
            $data[] = (int) ($dailyRows[$cursor->toDateString()] ?? 0);
            $cursor->addDay();
        }

        return view('admin.reports.analytics', [
            'byService' => $byService,
            'byCounter' => $byCounter,
            'avgWait' => round($avgWait ?? 0),
            'avgServiceTime' => round($avgServiceTime ?? 0),
            'chart' => ['labels' => $labels, 'data' => $data],
            'places' => Place::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);

        $tokens = QueueToken::with(['user', 'service', 'counter', 'place'])
            ->whereBetween('date', [$from, $to])
            ->when($request->place, fn ($q) => $q->where('place_id', $request->place))
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Token', 'Date', 'Customer', 'Phone', 'Place', 'Service', 'Counter', 'Status', 'Issued', 'Called', 'Completed']);

        foreach ($tokens as $t) {
            fputcsv($handle, [
                $t->token_no,
                $t->date,
                $t->user?->name,
                $t->user?->phone,
                $t->place?->name,
                $t->service?->name,
                $t->counter?->name,
                $t->status,
                optional($t->issued_at)->format('H:i'),
                optional($t->called_at)->format('H:i'),
                optional($t->completed_at)->format('H:i'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="queue-report-'.$from.'-'.$to.'.csv"',
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function range(Request $request): array
    {
        $from = $request->filled('from') ? $request->from : now()->subDays(6)->toDateString();
        $to = $request->filled('to') ? $request->to : now()->toDateString();

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
