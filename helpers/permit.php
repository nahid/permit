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


if (!function_exists('json_to_array')) {
    function json_to_array($json)
    {
        $json_out = json_decode($json, true);
        if (is_string($json_out) || is_null($json_out)) {
            return [];
        }

        return $json_out;
    }
}

if (!function_exists('array_merge_nested')) {
    function array_merge_nested(array &$array1, array &$array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_nested($merged[$key], $value);
            } else {
                if (is_string($key)) {
                    $merged[$key]  = $value;
                } else {
                    $merged[]  = $value;
                }
            }
        }

        return $merged;
    }
}