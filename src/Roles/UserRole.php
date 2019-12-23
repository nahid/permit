<?php

namespace Nahid\Permit\Roles;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{

    public $timestamps = false;

    public $fillable = [
        'role_name',
        'permission',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('permit.user_permissions_table', 'user_permissions');

    }

}
