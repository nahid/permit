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
        return Role::class;
    }


    public function setUserRole($user_id, $role_id)
    {
        $data = [
            'user_id' => $user_id,
            'role_id' => $role_id
        ];

        return $this->create($data);
    }
}
