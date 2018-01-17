<?php

namespace Nahid\Permit\Users;

trait Permitable
{
    /**
     * boot model
     */
    protected static function boot()
    {
        parent::boot();

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
     * relationship for permission
     *
     * @return mixed
     */
    public function permission()
    {
        return $this->belongsTo('Nahid\Permit\Permissions\Permission', config('permit.users.role_column'), 'role_name');
    }
}
