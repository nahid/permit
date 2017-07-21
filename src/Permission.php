<?php

namespace Nahid\Permit;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Config\Repository;
use Nahid\JsonQ\Jsonq;

use Nahid\Permit\Permissions\PermissionRepository;
use Nahid\Permit\Users\UserRepository;

class Permission
{
    protected $superUser;
    protected $roleColumn;
    protected $config;
    protected $permission;
    protected $userModelNamespace;
    protected $userModel;
    protected $user;
    protected $json;
    protected $authPermissions = [];

    function __construct(Repository $config, PermissionRepository $permission, UserRepository $user)
    {
        $this->config = $config;
        $this->permission = $permission;
        $this->user = $user;
        $this->userModelNamespace = $this->config->get('permit.users.model');
        $this->superUser = $this->config->get('permit.super_user');
        $this->roleColumn = $this->config->get('permit.users.role_column');
        $this->userModel = new $this->userModelNamespace;

        $this->json = new Jsonq();
    }


    public function userAllows($user, $permission)
    {

        if ($user instanceof $this->userModelNamespace) {
            if ($user->{$this->roleColumn} == $this->superUser) {
                return true;
            }

            if(!empty($user->permissions)) {
                $permissions = json_decode($user->permissions);
                $permit = explode(':', $permission);
                $json = $this->json->collect($permissions);

                $this->authPermissions = (array) $json->node($permit[0])->get(false);

                if (count($permit) === 1) {
                    if ($this->getAuthPermissions()>0) {
                        return true;
                    }
                }

                if (count($permit)>1) {
                    if ($this->isPermissionDo($permit[1])) {
                        return true;
                    }
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

        if ($user instanceof $this->userModelNamespace) {
            if ($user->{$this->roleColumn} == $this->superUser) {
                return true;
            }

            if (!is_null($user->permission))
            {
                $permissions = json_decode($user->permission->permission);
                $permit = explode(':', $permission);
                $json = $this->json->collect($permissions);

                $this->authPermissions = (array) $json->node($permit[0])->get(false);

                if (count($permit) === 1) {
                    if ($this->getAuthPermissions()>0) {
                        return true;
                    }
                }

                if (count($permit)>1) {
                    if ($this->isPermissionDo($permit[1])) {
                        return true;
                    }
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
        if ($user instanceof $this->userModelNamespace) {
            $user_json = new Jsonq();
            $role_json = new Jsonq();

            $user_permissions = json_decode($user->permissions);
            $role_permissions = json_decode($user->permission->permission);

            $permit = explode(':', $permission);

            $role_permit = $role_json->collect($role_permissions);
            $role_auth_permissions = (array) $role_permit->node($permit[0])->get(false);

            $user_permit = $user_json->collect($user_permissions);
            $user_auth_permissions = (array) $user_permit->node($permit[0])->get(false);

            $this->authPermissions = array_merge($role_auth_permissions, $user_auth_permissions);

            if (count($permit) === 1) {
                if ($this->getAuthPermissions()>0) {
                    return true;
                }
            }

            if (count($permit)>1) {
                if ($this->isPermissionDo($permit[1])) {
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


    protected function isPermissionDo($permission)
    {
        if (is_null($permission)) {
            return false;
        }

        if (isset($this->authPermissions[$permission])) {
            if ($this->authPermissions[$permission]) {
                return true;
            }
        }

        return false;
    }


    protected function getAuthPermissions()
    {
        $permissions = $this->authPermissions;
        $auth_permissions = array_filter($permissions, function($value) {
            return $value === true;
        });

        return count($auth_permissions);
    }



}