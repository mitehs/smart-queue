<?php

namespace App\Services;

use App\Models\QueueToken;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class TokenGenerator
{
    /**
     * Generate the next daily token number for a service, e.g. BC-014.
     * Resets every day because the sequence is counted per date.
     */
    public function next(Service $service): string
    {
        return DB::transaction(function () use ($service) {
            $date = now()->toDateString();

            $last = QueueToken::query()
                ->where('service_id', $service->id)
                ->where('date', $date)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('token_no');

            $sequence = 1;

            if ($last) {
                $parts = explode('-', $last);
                $candidate = (int) end($parts);
                $sequence = $candidate + 1;
            }

            return sprintf('%s-%03d', $service->code, $sequence);
        });
    }

    /**
     * Number of people ahead of a token in its counter queue (open statuses only).
     */
    public function peopleAhead(QueueToken $token): int
    {
        if (! $token->counter_id) {
            return QueueToken::where('service_id', $token->service_id)
                ->where('date', $token->date)
                ->where('status', QueueToken::STATUS_WAITING)
                ->where('issued_at', '<', $token->issued_at)
                ->count();
        }

        return QueueToken::where('counter_id', $token->counter_id)
            ->where('date', $token->date)
            ->where('status', QueueToken::STATUS_WAITING)
            ->where('issued_at', '<', $token->issued_at)
            ->count();
    }
}
