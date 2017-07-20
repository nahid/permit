<?php

namespace Nahid\Permit;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Config\Repository;
use Nahid\JsonQ\Jsonq;

use Nahid\Permit\Permissions\PermissionRepository;
use Nahid\Permit\Users\UserRepository;

class Permission
{
    protected $config;
    protected $permission;
    protected $userModel;
    protected $user;
    protected $json;
    protected $userCanDo = [];
    protected $roleCanDo = [];

    function __construct(Repository $config, PermissionRepository $permission, UserRepository $user)
    {
        $this->config = $config;
        $this->permission = $permission;
        $this->user = $user;
        $user_model = $this->config->get('permit.users.model');
        $this->userModel = new $user_model;
        $this->json = new Jsonq();
    }


    public function userAllows($user, $permission)
    {

        $user_model =$this->config->get('permit.users.model');

        if ($user instanceof $user_model) {
            if(!empty($user->permissions)) {
                $permissions = json_decode($user->permissions);
                $permit = explode(':', $permission);
                $json = $this->json->collect($permissions);

                $this->userCanDo = $json->node($permit[0])->get(false);
                if ($this->isUserDo($permit[1])) {
                    return true;
                }
            }
        }

        throw new AuthorizationException("Unauthorized");

    }

    public function userCan($user, $permission)
    {
        try {
            return $this->userAllows($user, $permission);
        } catch (AuthorizationException $e) {
            return false;
        }
    }



    public function roleAllows($user, $permission)
    {
        $user_model =$this->config->get('permit.users.model');
        if ($user instanceof $user_model) {
            if (!is_null($user->permission))
            {
                $permissions = json_decode($user->permission->permission);
                $permit = explode(':', $permission);
                $json = $this->json->collect($permissions);
                $this->roleCanDo = $json->node($permit[0])->get(false);
                if ($this->isRoleDo($permit[1])) {
                    return true;
                }
            }
        }

        throw new AuthorizationException("Unauthorized");
    }


    public function roleCan($user, $permission)
    {

        try {
            return $this->roleAllows($user, $permission);
        } catch (AuthorizationException $e) {
            return false;
        }
        
    }


    public function allows($user, $permission)
    {
        $user_model =$this->config->get('permit.users.model');

        if ($user instanceof $user_model) {
            $user_permissions = json_decode($user->permissions);
            $role_permissions = json_decode($user->permission->permission);
            $permit = explode(':', $permission);
            $role_permit = $this->json->collect($role_permissions);
            $user_permit = $this->json->collect($user_permissions);

            $this->userCanDo = $user_permit->node($permit[0])->get(false);
            $this->roleCanDo = $role_permit->node($permit[0])->get(false);


            if ($this->isRoleDo($permit[1])) {
                if ($this->isUserDo($permit[1])) {
                    return true;
                }
            }else {
                if ($this->isUserDo($permit[1])) {
                    return true;
                }
            }
        }

        throw new AuthorizationException("Unauthorized");

    }


    public function can($user, $permission)
    {
        try {
            return $this->allows($user, $permission);
        } catch (AuthorizationException $e) {
            return false;
        }
    }



    protected function isUserDo($permission)
    {
        if (is_null($permission)) {
            return false;
        }

        if (isset($this->userCanDo[$permission])) {
            if ($this->userCanDo[$permission]) {
                return true;
            }
        }

        return false;
    }


    public function setRolePermission($role_name, $service, $permissions = [])
    {
        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_decode($role->permission, true);
            foreach ($permissions as $name=>$val) {
                $permission[$service][$name] = $val;
            }

            $role->update(['permission'=>$permission]);
            
        } else {
            $row = ['role_name'=>$role_name, 'permission'=>[]];
            foreach ($permissions as $name=>$val) {
                $row['permission'][$service][$name] = $val;
            }

            //dd($row);

            $this->permission->create($row);
        }



        return true;
    }

    public function setUserPermission($user_id, $service, $permissions = [])
    {
        $user = $this->user->find($user_id);

        if ($user) {
            $permission = json_decode($user->permissions, true);
            foreach ($permissions as $name=>$val) {
                $permission[$service][$name] = $val;
            }

            $this->userModel->unguard();
            $this->user->update($user_id, ['permissions'=>$permission]);
            $this->userModel->reguard();
        }



        return true;
    }


    public function setUserRole($user_id, $role_name)
    {
        $user = $this->user->find($user_id);

        if ($user) {
            $this->userModel->unguard();
            $this->user->update($user_id, [$this->config->get('permit.users.role_column')=>$role_name]);
            $this->userModel->reguard();
            return true;
        }
    }


    protected function isRoleDo($permission)
    {
        if (is_null($permission)) {
            return false;
        }

        if (isset($this->roleCanDo[$permission])) {
            if ($this->roleCanDo[$permission]) {
                return true;
            }
        }

        return false;
    }



}