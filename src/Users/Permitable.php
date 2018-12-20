<?php

namespace Nahid\Permit\Users;

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
        if ($this->relationLoaded('permission')) {
            $role_permissions = json_to_array($this->permission->permission);
        }
        $user_permissions = json_to_array($this->attributes['permissions']);

        return array_merge_nested($role_permissions, $user_permissions);
    }

    /**
     * relationship for permission
     *
     * @return mixed
     */
    public function permission()
    {
        return $this->belongsTo('Nahid\Permit\Permissions\Permission', config('permit.users.role_column'), 'role_name');
    }

}
