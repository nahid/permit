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

    public function __construct(Repository $config, PermissionRepository $permission, UserRepository $user)
    {
        $this->config = $config;
        $this->permission = $permission;
        $this->user = $user;
        $this->userModelNamespace = $this->config->get('permit.users.model');
        $this->superUser = $this->config->get('permit.super_user');
        $this->roleColumn = $this->config->get('permit.users.role_column');
        $this->userModel = new $this->userModelNamespace();

        $this->json = new Jsonq();
    }

    public function userAllows($user, $permission, $params = [])
    {
        if ($user instanceof $this->userModelNamespace) {
            if ($user->{$this->roleColumn} == $this->superUser) {
                return true;
            }

            if (!empty($user->permissions)) {
                $abilities = json_decode($user->permissions);
                $this->authPermissions = $this->json->collect($abilities);

                if (!is_null($user->permission)) {

                    if (is_array($permission)) {
                        if ($this->hasOnePermission($permission, $user)) {
                            return true;
                        }
                    }


                    if (is_string($permission)) {
                        if ($this->isPermissionDo($permission, $user, $params)) {
                            return true;
                        }
                    }
                }
            }
        }

        throw new AuthorizationException('Unauthorized');
    }

    public function userCan($user, $permission, $params = [])
    {
        try {
            return $this->userAllows($user, $permission, $params);
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    public function roleAllows($user, $permission, $params = [])
    {
        if ($user instanceof $this->userModelNamespace) {
            if ($user->{$this->roleColumn} == $this->superUser) {
                return true;
            }

            $abilities = json_decode($user->permission->permission);
            $this->authPermissions = $this->json->collect($abilities);

            if (!is_null($user->permission)) {

                if (is_array($permission)) {
                    if ($this->hasOnePermission($permission, $user)) {
                        return true;
                    }
                }


                if (is_string($permission)) {
                    if ($this->isPermissionDo($permission, $user, $params)) {
                        return true;
                    }
                }
            }
        }

        throw new AuthorizationException('Unauthorized');
    }

    public function roleCan($user, $permission, $params = [])
    {
        try {
            return $this->roleAllows($user, $permission, $params);
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    public function allows($user, $permission, $params = [])
    {
        if ($user instanceof $this->userModelNamespace) {

            $user_permissions = json_decode($user->permissions, true);
            $role_permissions = json_decode($user->permission->permission, true);
            $abilities = array_merge($role_permissions, $user_permissions);

            $this->authPermissions = $this->json->collect($abilities);

            if (count($abilities) > 0) {

                if (is_array($permission)) {
                    if ($this->hasOnePermission($permission, $user)) {
                        return true;
                    }
                }


                if (is_string($permission)) {
                    if ($this->isPermissionDo($permission, $user, $params)) {
                        return true;
                    }
                }
            }


        }

        throw new AuthorizationException('Unauthorized');
    }

    public function can($user, $permission, $params = [])
    {
        try {
            return $this->allows($user, $permission, $params);
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    public function setRolePermission($role_name, $service, $permissions = [])
    {
        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_decode($role->permission, true);
            foreach ($permissions as $name => $val) {
                $permission[$service][$name] = $val;
            }

            $role->update(['permission' => $permission]);
        } else {
            $row = ['role_name' => $role_name, 'permission' => []];
            foreach ($permissions as $name => $val) {
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
            foreach ($permissions as $name => $val) {
                $permission[$service][$name] = $val;
            }

            $this->userModel->unguard();
            $this->user->update($user_id, ['permissions' => $permission]);
            $this->userModel->reguard();
        }

        return true;
    }

    public function setUserRole($user_id, $role_name)
    {
        $user = $this->user->find($user_id);

        if ($user) {
            $this->userModel->unguard();
            $this->user->update($user_id, [$this->config->get('permit.users.role_column') => $role_name]);
            $this->userModel->reguard();

            return true;
        }
    }

    protected function callPolicy($callable, $params = [])
    {
        $arr_callable = explode('@', $callable);

        if (count($arr_callable)>1) {
            $class = new $arr_callable[0]();
            $method = $arr_callable[1];

            return call_user_func_array([$class, $method], $params);
        }

        return false;
    }

    protected function isPermissionDo($permission, $user, $params = [])
    {
        $parameters = [$user];

        $permit = explode(':', $permission);

        if (count($permit) == 2) {
            $auth_permissions = (array) $this->authPermissions->node($permit[0])->get(false);
            foreach ($params as $param) {
                array_push($parameters, $param);
            }


            if (is_null($permission)) {
                return false;
            }

            if (isset($auth_permissions[$permit[1]])) {
                if ($auth_permissions[$permit[1]] === true) {
                    return true;
                } else if (is_string($auth_permissions[$permit[1]])) {
                    return $this->callPolicy($auth_permissions[$permit[1]], $parameters);
                }
            }
        }
        return false;
    }

    protected function hasOnePermission($permissions = [], $user)
    {
        foreach ($permissions as $key => $value) {
            $permission = '';
            $params = [];
            if (is_int($key)) {
                $permission = $value;
            } else {
                $permission = $key;
                $params = $value;
            }


            if ($this->isPermissionDo($permission, $user, $params)) {
                return true;
            }
        }

        return false;
    }
}
