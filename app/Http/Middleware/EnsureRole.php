<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage: ->middleware('role:admin') or 'role:admin,staff'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Contact the administrator.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            return redirect()->to($this->homeFor($user->role));
        }

        return $next($request);
    }

    public function homeFor(string $role): string
    {
        return match ($role) {
            'admin' => '/admin',
            'staff' => '/staff',
            default => '/dashboard',
        };
    }
}
