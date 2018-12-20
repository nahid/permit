<?php

namespace Nahid\Permit\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static bool userAllows(\Illuminate\Database\Eloquent\Model $user, string|array $permission, array $param)
 * @method static bool userCan(\Illuminate\Database\Eloquent\Model $user, string|array $permission, array $param)
 * @method static bool roleAllows(\Illuminate\Database\Eloquent\Model $role, string|array $permission, array $param)
 * @method static bool roleCan(\Illuminate\Database\Eloquent\Model $role, string|array $permission, array $param)
 * @method static bool allows(\Illuminate\Database\Eloquent\Model $role, string|array $permission, array $param)
 * @method static bool can(\Illuminate\Database\Eloquent\Model $role, string|array $permission, array $param)
 * @method static bool setUserRole(int $user_id, string $role_name)
 * @method static bool setUserPermissions(int $user_id, string $module, array $abilities)
 * @method static bool setRolePermissions(int $user_id, string $module, array $abilities)
 * @method static array getAbilities(string $module)
 * @method static array roles()
 * @method static mixed role()
 */
class Permit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'permit';
    }
}
