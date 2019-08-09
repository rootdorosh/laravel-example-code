<?php

namespace App\Http\Middleware;

use App\Exceptions\UserNotActiveException;
use Closure;

class ActiveUser
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()->is_active) {
            throw new UserNotActiveException;
        }

        return $next($request);
    }
}
