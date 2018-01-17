<?php

namespace Nahid\Permit\Middleware;

use Closure;

abstract class AbstractMiddleware
{
    /**
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @param $permission
     * @param $params
     *
     * @return bool
     */
    abstract protected function permission($permission, $params = []);

    public function handle($request, Closure $next, $permission, $params)
    {
        if ($this->permission($permission, $params)) {
            return $next($request);
        }

        return redirect($this->redirectTo);
    }
}
