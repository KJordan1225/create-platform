<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCreator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isApprovedCreator() || ! $user->is_active) {
            abort(403, 'Creator access only.');
        }

        return $next($request);
    }
}
