<?php

namespace LangtonMwanza\PulseDevops\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeDevops
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('pulse-devops.authorize', true)) {
            abort(403, 'Pulse DevOps is disabled.');
        }

        if (Gate::has('viewPulseDevops') && Gate::denies('viewPulseDevops')) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
