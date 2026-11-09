<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Single session locking only applies to voter accounts
            if ($user->hasRole('voters') && config('election.enable_single_session', true)) {
                $sessionId = $request->session()->getId();

                // If user has not voted yet, strictly enforce single device session
                if (! $user->sudahVoting()) {
                    if ($user->current_session_id && $user->current_session_id !== $sessionId) {
                        // Another device became active or session mismatched
                        Auth::guard('web')->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()->route('login')->withErrors([
                            'email' => 'Sesi bilik suara Anda telah terkunci di perangkat lain atau direset oleh Panitia TPS demi integritas 1 orang 1 suara.',
                        ]);
                    }

                    // Register session if not yet locked
                    if (empty($user->current_session_id)) {
                        $user->lockSession($sessionId, $request->ip(), $request->userAgent());
                    } elseif (! $user->session_last_active_at || now()->diffInSeconds($user->session_last_active_at) >= 30) {
                        // Throttled heartbeat update every 30s
                        $user->updateQuietly([
                            'session_last_active_at' => now(),
                            'session_ip'             => $request->ip(),
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}

