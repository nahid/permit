<?php

namespace Nahid\Permit\Middleware;

class PermitMiddleware extends AbstractMiddleware
{
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
