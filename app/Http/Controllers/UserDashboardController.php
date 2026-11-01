<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\QueueToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $active = QueueToken::with(['service', 'counter', 'place'])
            ->where('user_id', $user->id)
            ->whereIn('status', [QueueToken::STATUS_WAITING, QueueToken::STATUS_CALLED, QueueToken::STATUS_SERVING])
            ->latest('id')
            ->first();

        $stats = [
            'total' => QueueToken::where('user_id', $user->id)->count(),
            'done' => QueueToken::where('user_id', $user->id)->where('status', QueueToken::STATUS_DONE)->count(),
            'today' => QueueToken::where('user_id', $user->id)->where('date', now()->toDateString())->count(),
            'waited' => (int) QueueToken::where('user_id', $user->id)
                ->whereNotNull('called_at')
                ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, issued_at, called_at)), 0)')
                ->value('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, issued_at, called_at)), 0)'),
        ];

        $recent = QueueToken::with(['service', 'counter', 'place'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('user.dashboard', compact('active', 'stats', 'recent'));
    }

    public function history(Request $request)
    {
        $tokens = QueueToken::with(['service', 'counter', 'place'])
            ->where('user_id', auth()->id())
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q, $term) => $q->where('token_no', 'like', "%{$term}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('user.history', compact('tokens'));
    }

    public function notifications()
    {
        $user = auth()->user();

        $notifications = $user->notifications()->latest()->paginate(15);

        // Build queue alerts from live tokens (simple, no queue worker needed)
        $active = QueueToken::with(['service', 'counter'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('user.notifications', compact('notifications', 'active'));
    }

    public function help()
    {
        return view('user.help');
    }

    public function contact()
    {
        return view('user.contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        // Store in settings log so it is visible in admin (no mail server needed on XAMPP)
        $inbox = json_decode(\App\Models\Setting::get('contact_inbox', '[]') ?: '[]', true);
        $inbox[] = $data + ['at' => now()->toDateTimeString()];
        \App\Models\Setting::set('contact_inbox', json_encode($inbox));

        return redirect()->route('contact')->with('success', 'Thanks! Your message has been sent.');
    }

    public function contribution()
    {
        $user = auth()->user();

        $byService = QueueToken::with('service')
            ->where('user_id', $user->id)
            ->selectRaw('service_id, COUNT(*) as c')
            ->groupBy('service_id')
            ->orderByDesc('c')
            ->get();

        $monthly = QueueToken::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfYear())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as m, COUNT(*) as c")
            ->groupBy('m')
            ->orderBy('m')
            ->pluck('c', 'm');

        return view('user.contribution', compact('byService', 'monthly'));
    }
}
