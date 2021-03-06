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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('permit.connection'));

        $this->table = config('permit.user_roles_table', 'user_roles');

    }

}
