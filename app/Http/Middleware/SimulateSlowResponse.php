<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SimulateSlowResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $delay = 1): Response
    {
        // Apply delay only if APP_ENV is 'local'
        if (App::environment('local')) {
            // Enforce a maximum delay of 10 seconds
            $delay = min(intval($delay), 10);

            // Simulate delay
            sleep($delay);
        }

        return $next($request);
    }
}
