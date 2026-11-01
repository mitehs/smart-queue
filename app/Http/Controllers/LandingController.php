<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\QueueToken;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $places = Place::where('is_active', true)->withCount('services')->get();

        // Live "now serving" ticker across all counters
        $nowServing = QueueToken::with(['counter', 'service', 'place'])
            ->where('date', now()->toDateString())
            ->whereIn('status', [QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('called_at')
            ->limit(8)
            ->get();

        $todayServed = QueueToken::where('date', now()->toDateString())
            ->where('status', QueueToken::STATUS_DONE)
            ->count();

        return view('welcome', compact('places', 'nowServing', 'todayServed'));
    }
}
