<?php

namespace Nahid\Permit\Permissions;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $table = 'permissions';

    public $timestamps = false;

    public $fillable = [
        'role_name',
        'permission'
    ];


    /*public function getPermissionsAttribute()
    {
        return json_decode($this->attributes['permission'], true);
    }*/

    public function setPermissionAttribute($value)
    {
        return $this->attributes['permission'] = json_encode($value);
    }
}
