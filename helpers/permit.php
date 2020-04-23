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
        if (is_array($json)) {
            return $json;
        }

        $json_out = json_decode($json, true);
        if (is_string($json_out) || is_null($json_out)) {
            return [];
        }

        return $json_out;
    }
}

if (!function_exists('array_merge_nested')) {
    function array_merge_nested(array &$array1, array &$array2, $priority = true)
    {
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_nested($merged[$key], $value, $priority);
            } else {
                if (is_string($key)) {
                    if ($value === null) {
                        continue;
                    }

                    if ($priority) {
                        $merged[$key]  = $value != false ? $value : ($merged[$key] ?? false);
                    } else {
                        $merged[$key]  =  $value;
                    }
                } else {
                    $merged[]  = $value;
                }
            }
        }

        return $merged;
    }
}

if (!function_exists('array_multidimensional_diff')) {
    function array_multidimensional_diff($array1, $array2)
    {
        $result = array();

        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $recursiveArrayDiff = array_multidimensional_diff($value, $array2[$key]);

                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('array_value_replace')) {
    function array_value_replace(array $array, array $replace)
    {
        $data = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_value_replace($value, $replace);
                continue;
            } else {
                if(is_bool($value)) {
                    $data[$key] = $value;
                    continue;
                }

                if (array_key_exists($value, $replace)) {
                    $data[$key] = $replace[$value];
                    continue;
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }
}
