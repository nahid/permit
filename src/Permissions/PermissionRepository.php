<?php

namespace Nahid\Permit\Permissions;

use Nahid\Permit\BaseRepository;

class PermissionRepository extends BaseRepository
{
    protected function setModel()
    {
        return 'Nahid\Permit\Permissions\Permission';
    }

    public function syncRolePermissions($role, array $data)
    {
        $record = $this->model->where('role_name', $role);
        if ($record->exists()) {
            return $record->update($data);
        }

        return $this->model->insert($data);
    }
}
