<?php

namespace Nahid\Permit\Middleware;

class PermitMiddleware extends AbstractMiddleware
{
    protected function permission($permission)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (can_do($user, $permission)) {
                return true;
            }
        }

        return false;
    }
}
