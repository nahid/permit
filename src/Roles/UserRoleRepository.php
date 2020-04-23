<?php

namespace Nahid\Permit\Roles;

use Nahid\Permit\BaseRepository;

class UserRoleRepository extends BaseRepository
{
    /**
     * take model namespace
     *
     * @return string
     */
    protected function setModel()
    {
        return UserRole::class;
    }


    public function setUserRole($user_id, $role_id)
    {
        if ($this->isAlreadyAssigned($user_id, $role_id)) {
            return false;
        }

        $data = [
            'user_id' => $user_id,
            'role_id' => $role_id
        ];

        return $this->create($data);
    }

    public function setBulkUserRole($user_id, array $role_ids)
    {
        $data = [];

        foreach($role_ids as $id) {
            $data[] = ['user_id' => $user_id, 'role_id' => $id];
        }

        $this->unassignedUserRoles($user_id);

        return $this->insert($data);

    }

    public function unassignedUserRoles($user_id)
    {
        return $this->model->where('user_id', $user_id)->delete();
    }

    public function isAlreadyAssigned($user_id, $role_id)
    {
        return $this->model
            ->where('user_id', $user_id)
            ->where('role_id', $role_id)
            ->first();
    }
}
