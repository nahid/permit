<?php

namespace Nahid\Permit\Users;

use Rinvex\Repository\Repositories\EloquentRepository;

class UserRepository extends EloquentRepository
{
    protected $repositoryId = 'permit.repository.user';

    protected $model;

    function __construct()
    {
        $this->model = config('permit.users.model');
    }


}
