<?php

namespace Nahid\Permit\Roles;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    public $fillable = [
        'role_name',
        'permission',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('permit.roles_table', 'roles');
    }

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
