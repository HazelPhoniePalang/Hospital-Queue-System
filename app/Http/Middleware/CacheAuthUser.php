<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CacheAuthUser
{
    public function handle($request, Closure $next)
    {
        if ($userId = session('login_web_' . sha1(\Illuminate\Auth\SessionGuard::class))) {
            $user = Cache::remember("auth_user_{$userId}", now()->addMinutes(5), function () use ($userId) {
                return \App\Models\User::find($userId);
            });

            if ($user) {
                Auth::setUser($user);
            }
        }

        return $next($request);
    }
}