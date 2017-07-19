<?php

namespace Nahid\Permit\Permissions;

use Nahid\Permit\BaseRepository;

class PermissionRepository extends BaseRepository
{

    protected function setModel()
    {
        return 'Nahid\Permit\Permissions\Permission';
    }

}
