<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\QueueToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QueueTokenSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', User::ROLE_USER)->get();
        $counters = Counter::with('service')->where('is_active', true)->get();

        if ($customers->isEmpty() || $counters->isEmpty()) {
            return;
        }

        // Sequence tracker so token numbers stay unique per service per date
        // (two counters can share the same service).
        $seq = [];

        $nextNo = function (int $serviceId, string $date) use (&$seq): string {
            $key = $serviceId.'|'.$date;
            $seq[$key] = ($seq[$key] ?? 0) + 1;

            $service = \App\Models\Service::find($serviceId);

            return sprintf('%s-%03d', $service->code, $seq[$key]);
        };

        $closedStatuses = ['done', 'done', 'done', 'done', 'done', 'skipped', 'no_show', 'cancelled'];

        /* ---------------- PAST 6 DAYS (history for charts/reports) --------------- */
        for ($daysAgo = 6; $daysAgo >= 1; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo);

            foreach ($counters as $counter) {
                if (! $counter->service) {
                    continue;
                }

                $total = rand(5, 11);

                for ($i = 0; $i < $total; $i++) {
                    $issued = $date->copy()->setTime(rand(9, 16), rand(0, 59));
                    $status = $closedStatuses[array_rand($closedStatuses)];
                    $called = in_array($status, ['cancelled'], true)
                        ? null
                        : $issued->copy()->addMinutes(rand(4, 45));

                    QueueToken::create([
                        'token_no' => $nextNo($counter->service_id, $date->toDateString()),
                        'user_id' => $customers->random()->id,
                        'place_id' => $counter->place_id,
                        'service_id' => $counter->service_id,
                        'counter_id' => $counter->id,
                        'status' => $status,
                        'issued_at' => $issued,
                        'called_at' => $called,
                        'completed_at' => $called?->copy()->addMinutes(rand(3, 20)),
                        'served_by' => $counter->staff_id,
                        'date' => $date->toDateString(),
                    ]);
                }
            }
        }

        /* ---------------- TODAY (live demo state) -------------------------------- */
        $rahul = $customers->firstWhere('email', 'user@smartqueue.test');
        $sneha = $customers->firstWhere('email', 'sneha@smartqueue.test');

        foreach ($counters as $counter) {
            if (! $counter->service) {
                continue;
            }

            $date = Carbon::today();
            $total = rand(7, 12);

            // First ~60% are done, 1 is being served, the rest are waiting
            $doneCount = (int) floor($total * 0.6);

            for ($i = 1; $i <= $total; $i++) {
                $issued = $date->copy()->setHour(rand(9, max(9, (int) now()->format('H')) - 1 ?: 9))
                    ->setMinute(rand(0, 59))
                    ->setSecond(0);

                // Keep issued_at in the past
                if ($issued->isFuture()) {
                    $issued = now()->copy()->subMinutes(rand(10, 120));
                }

                if ($i <= $doneCount) {
                    $status = rand(1, 12) === 1 ? 'skipped' : 'done';
                    $called = $issued->copy()->addMinutes(rand(4, 30));
                    $completed = $called->copy()->addMinutes(rand(3, 18));
                } elseif ($i === $doneCount + 1) {
                    $status = 'called';
                    $called = now()->copy()->subMinutes(rand(1, 6));
                    $completed = null;
                } else {
                    $status = 'waiting';
                    $called = null;
                    $completed = null;
                }

                // Give Rahul & Sneha an active token at two different counters
                $userId = $customers->random()->id;
                if ($i === $doneCount + 2 && $rahul && $counter->id === $counters->first()->id) {
                    $userId = $rahul->id;
                    $issued = now()->copy()->subMinutes(18);
                }
                if ($i === $doneCount + 2 && $sneha && $counter->id === $counters->last()->id) {
                    $userId = $sneha->id;
                    $issued = now()->copy()->subMinutes(9);
                }

                QueueToken::create([
                    'token_no' => $nextNo($counter->service_id, $date->toDateString()),
                    'user_id' => $userId,
                    'place_id' => $counter->place_id,
                    'service_id' => $counter->service_id,
                    'counter_id' => $counter->id,
                    'status' => $status,
                    'issued_at' => $issued,
                    'called_at' => $called,
                    'completed_at' => $completed,
                    'served_by' => $completed ? $counter->staff_id : null,
                    'date' => $date->toDateString(),
                ]);
            }
        }
    }
}
