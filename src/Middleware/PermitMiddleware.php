<?php

namespace Nahid\Permit\Middleware;

class PermitMiddleware extends AbstractMiddleware
{
    /**
     * make a permission for middleware
     *
     * @param       $permission
     * @param array $params
     * @return bool
     */
    protected function permission($permission, $params = [])
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (can_do($user, $permission, $params)) {
                return true;
            }
        }

        return false;
    }
}
