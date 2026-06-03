<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuditor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) $request->user()?->role_id !== 5) {
            abort(403);
        }

        return $next($request);
    }
}
