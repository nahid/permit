<?php

namespace Nahid\Permit\Users;

use Nahid\Permit\BaseRepository;

class UserRepository extends BaseRepository
{

    protected function setModel()
    {
        return config('permit.users.model');
    }

}
