<?php

namespace Nahid\Permit\Permissions;

use Rinvex\Repository\Repositories\EloquentRepository;

class PermissionRepository extends EloquentRepository
{
    protected $repositoryId = 'permit.repository.permission';

    protected $model = 'Nahid\Permit\Permissions\Permission';



    function __construct()
    {

    }


}
