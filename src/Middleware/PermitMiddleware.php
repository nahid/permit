<?php

namespace Nahid\Permit\Middleware;

use Closure;

class PermitMiddleware
{

    public function handle($request, Closure $next, $permission)
    {
        if(auth()->check()) {
            $user = auth()->user();
            if (can_do($user, $permission)) {
                return $next($request);
            }
        }

        return redirect('/login');

    }

}