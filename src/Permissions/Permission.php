<?php

namespace Nahid\Permit\Permissions;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $table = 'permissions';

    public $timestamps = false;

    public $fillable = [
        'role_name',
        'permission',
    ];

    /*public function getPermissionsAttribute()
    {
        return json_to_array($this->attributes['permission']);
    }*/

    public function setPermissionAttribute($value)
    {
        return $this->attributes['permission'] = json_encode($value);
    }

    /**
     * accessor for getting permissions
     *
     * @return array|mixed
     */
    public function getPermissionArrayAttribute()
    {
        return json_to_array($this->permission);
    }

}
