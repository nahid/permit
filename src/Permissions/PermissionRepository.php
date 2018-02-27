<?php

namespace Nahid\Permit\Permissions;

use Nahid\Permit\BaseRepository;

class PermissionRepository extends BaseRepository
{
    /**
     * take model namespace
     *
     * @return string
     */
    protected function setModel()
    {
        return 'Nahid\Permit\Permissions\Permission';
    }

    /**
     * sync role permissions
     *
     * @param       $role
     * @param array $data
     * @return mixed
     */
    public function syncRolePermissions($role, array $data)
    {
        $record = $this->model->where('role_name', $role);
        if ($record->exists()) {
            return $record->update($data);
        }

        return $this->model->insert($data);
    }


    /**
     * get all roles
     *
     * @return mixed
     */
    public function getRoles()
    {
        return $this->model->all();
    }

    /**
     * getting a single role
     *
     * @param $role
     * @return mixed
     */
    public function getRole($role)
    {
        return $this->model->where('role_name', $role)->first();
    }
}
