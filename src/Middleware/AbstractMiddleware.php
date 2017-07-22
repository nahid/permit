<?php

namespace Nahid\Permit\Middleware;

use Closure;

abstract class AbstractMiddleware
{
    protected $redirectTo = '/';

    /**
     * @param $permission
     *
     * @return bool
     */
    abstract protected function permission($permission);

    public function handle($request, Closure $next, $permission)
    {
        if ($this->permission($permission)) {
            return $next($request);
        }

        return redirect($this->redirectTo);
    }
}
