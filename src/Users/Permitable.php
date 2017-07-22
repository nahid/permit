<?php

namespace Nahid\Permit\Users;

trait Permitable
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PermissionScope());
    }

    public function setPermissionsAttribute($value)
    {
        return $this->attributes['permissions'] = json_encode($value);
    }

    public function permission()
    {
        return $this->belongsTo('Nahid\Permit\Permissions\Permission', config('permit.users.role_column'), 'role_name');
    }
}
