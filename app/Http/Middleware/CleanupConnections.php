<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CleanupConnections
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Close database connection after each request on Render
        if (getenv('RENDER')) {
            DB::disconnect();
        }

        return $response;
    }
}