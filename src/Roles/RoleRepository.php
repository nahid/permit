<?php

namespace Nahid\Permit\Roles;

use Nahid\Permit\BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * sync role permissions
     *
     * @param       $role
     * @param array $data
     * @return mixed
     */
    public function syncRolePermissions($role, array $data, $roles)
    {
        $record = $this->model->where('role_name', $role);
        $status = false;
        if ($record->exists()) {
            $status = true;
            $record->update($data);
        } else {
            $status = $this->model->insert($data);
        }

        $this->deleteRoles($roles);
        return $status;
    }

    public function deleteRoles($roles)
    {
        return $this->model->whereIn('role_name', $roles)->delete();
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

    /**
     * take model namespace
     *
     * @return string
     */
    protected function setModel()
    {
        return 'Nahid\Permit\Roles\Role';
    }
}
