<?php

if (!function_exists('user_can')) {
    function user_can($user, $permission, $params = [])
    {
        $permit = app('permit');

        return $permit->userCan($user, $permission, $params);
    }
}

if (!function_exists('user_allows')) {
    function user_allows($user, $permission, $params = [])
    {
        $permit = app('permit');

        return $permit->userAllows($user, $permission, $params);
    }
}

if (!function_exists('role_can')) {
    function role_can($user, $permission, $params = [])
    {
        $permit = app('permit');

        return $permit->roleCan($user, $permission, $params);
    }
}

if (!function_exists('role_allows')) {
    function role_allows($user, $permission, $params = [])
    {
        $permit = app('permit');

        return $permit->roleAllows($user, $permission, $params);
    }
}

if (!function_exists('can_do')) {
    function can_do($user, $permission, $params = [])
    {
        $permit = app('permit');

        return $permit->can($user, $permission, $params);
    }
}

if (!function_exists('allows')) {
    function allows($user, $permission,  $params = [])
    {
        $permit = app('permit');

        return $permit->allows($user, $permission, $params);
    }
}