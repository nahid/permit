<?php

namespace Nahid\Permit\Users;

use Nahid\Permit\Roles\Role;
use Nahid\Permit\Roles\RoleRepository;
use Nahid\Permit\Roles\UserRoleRepository;

trait Permitable
{
    /**
     * boot trait
     */
    protected static function bootPermitable()
    {
        static::addGlobalScope(new PermissionScope());
    }

    /**
     * mutator for permissions column
     *
     * @param $value
     * @return string
     */
    public function setPermissionsAttribute($value)
    {
        return $this->attributes['permissions'] = json_encode($value);
    }

    /**
     * accessor for getting json as array
     *
     * @return array|mixed
     */
    public function getPermissionArrayAttribute()
    {
        return json_to_array($this->permissions);
    }

    public function getAbilitiesAttribute()
    {
        $role_permissions = [];

        if ($this->relationLoaded('roles')) {
            foreach($this->roles as $role) {
                $role_perms = json_to_array($role->permission);
                $role_permissions = array_merge_nested($role_permissions, $role_perms);
            }
        }

        $user_permissions = json_to_array($this->attributes['permissions']);

        $perm = array_merge_nested($role_permissions, $user_permissions, false);
        return $perm;
    }

    /**
     * relationship for permission
     *
     * @return mixed
     */
    public function permission()
    {
        return $this->belongsTo(Role::class, config('permit.users.role_column'), 'role_name');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, config('permit.user_roles_table', 'user_permissions'), 'user_id', 'role_id');
    }

    public function canDo($permission, $params = [])
    {
        return can_do($this, $permission, $params);
    }

    public function allows($permission, $params = [])
    {
        allows($this, $permission, $params);
    }


    public function setRole($name)
    {
        $role = app(RoleRepository::class)->findBy('role_name', $name);

        if ($role) {
            return app(UserRoleRepository::class)->setUserRole($this->id, $role->id);
        }

        return false;
    }


    public function setPermissions($module, $abilities = [])
    {
        $permission = json_to_array($this->permissions);

        foreach ($abilities as $name => $val) {
            if (is_bool($val)) {
                $permission[$module][$name] = $val;
            } elseif (is_string($val)) {
                $policy = $val;
                $permission[$module][$name] = $policy;
            }
        }

        $this->permissions = json_encode($permission);

        return $this->save();
    }


}
