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
        $value = array_value_replace($value, ['true' => true, 'false' => false]);
        $permissions = array_multidimensional_diff($value, $this->getRoleAbilities());

        return $this->attributes['permissions'] = json_encode($permissions);
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
        return $this->getAbilities();
    }

    public function getAbilities()
    {
        $role_permissions = $this->getRoleAbilities();

        $user_permissions = json_to_array($this->attributes['permissions']);

        $perm = array_merge_nested($role_permissions, $user_permissions, false);
        return $perm;
    }

    protected function getRoleAbilities()
    {
        $role_permissions = [];

        if ($this->relationLoaded('roles')) {
            foreach($this->roles as $role) {
                $role_perms = json_to_array($role->permission);
                $role_permissions = array_merge_nested($role_permissions, $role_perms);
            }
        }

        return $role_permissions;
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
