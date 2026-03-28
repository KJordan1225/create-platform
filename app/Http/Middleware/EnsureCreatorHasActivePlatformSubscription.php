<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCreatorHasActivePlatformSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_creator) {
            abort(403, 'Only creators may access this area.');
        }

        if (!$user->is_active) {
            return redirect()
                ->route('creator.dashboard')
                ->with('error', 'Your creator account is not active.');
        }

        if (!$user->hasActiveCreatorPlatformSubscription()) {
            return redirect()
                ->route('creator.billing.subscribe')
                ->with('error', 'You need an active creator plan before you can create posts.');
        }

        return $next($request);
    }
}
