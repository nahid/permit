<?php

namespace Nahid\Permit\Users;

use Nahid\Permit\Users\PermissionScope;

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
        return $this->hasOne('Nahid\Permit\Permissions\Permission', 'role_name', 'role');
    }

}
