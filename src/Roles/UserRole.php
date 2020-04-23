<?php

namespace Nahid\Permit\Roles;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('permit.user_roles_table', 'user_roles');

    }

}
