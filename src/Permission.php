<?php

namespace Nahid\Permit;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Config\Repository;
use Nahid\JsonQ\Jsonq;
use Nahid\Permit\Roles\RoleRepository;
use Nahid\Permit\Roles\UserRoleRepository;
use Nahid\Permit\Users\UserRepository;

class Permission
{

    /**
     * user table role column
     * @var mixed
     */
    protected $debugSuperuserMode;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var RoleRepository
     */
    protected $userRole;

    /**
     * user model namespace
     * @var mixed
     */
    protected $userModelNamespace;

    /**
     * auth user model name
     * @var
     */
    protected $userModel;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var Jsonq
     */
    protected $json;

    /**
     * @var array
     */
    protected $abilities = [];

    /**
     * Role constructor.
     *
     * @param Repository           $config
     * @param RoleRepository $role
     * @param UserRoleRepository $userRole
     * @param UserRepository       $user
     * @throws
     */
    public function __construct(Repository $config, RoleRepository $role, UserRoleRepository $userRole, UserRepository $user)
    {
        $this->config = $config;
        $this->role = $role;
        $this->userRole = $userRole;
        $this->user = $user;
        $this->userModelNamespace = $this->config->get('permit.users.model');
        $this->debugSuperuserMode = $this->config->get('permit.debug.superuser_mode', false);
        $this->userModel = new $this->userModelNamespace();

        $this->json = new Jsonq();
    }

    /**
     * check the given user are allows for then given permission.
     * here permission check for user specific permissions and role based permissions
     *
     * @param       $user
     * @param       $permission
     * @param array $params
     * @return bool
     * @throws AuthorizationException
     */
    public function allows($user, $permission, $params = [])
    {
        if ($user instanceof $this->userModelNamespace) {
            if ($this->debugSuperuserMode) {
                return true;
            }

            $this->abilities = $user->abilities;

            if (count($this->abilities) > 0) {
                if (is_array($permission)) {
                    if ($this->hasOnePermission($permission, $user)) {
                        return true;
                    }
                }

                if (is_string($permission)) {
                    if ($this->canUserDo($permission, $user, $params)) {
                        return true;
                    }
                }
            }
        }

        throw new AuthorizationException('Unauthorized');
    }

    /**
     * its an alias of allows, but its return bool instead of Exception
     *
     * @param       $user
     * @param       $permission
     * @param array $params
     * @return bool
     */
    public function can($user, $permission, $params = [])
    {
        try {
            return $this->allows($user, $permission, $params);
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    /**
     * this method is set user role for given user id
     *
     * @param $user_id
     * @param $role_name
     * @return bool
     */
    public function setUserRole($user_id, $role_name)
    {
        $user = $this->user->find($user_id);
        $role = $this->role->findBy('role_name', $role_name);

        if ($user && $role) {
            return $this->userRole->setUserRole($user_id, $role->id);
        }

        return false;
    }

    /**
     * fetch policy for given permission
     *
     * @param $ability
     * @return string
     */
    protected function fetchPolicy($ability)
    {
        return $ability;
    }

    /**
     * set permissions for given user
     *
     * @param       $user_id
     * @param       $module
     * @param array $abilities
     * @return bool
     */
    public function setUserPermissions($user_id, $module, $abilities = [])
    {
        $user = $this->user->find($user_id);
        if ($user) {
            $permission = json_to_array($user->permissions);
            foreach ($abilities as $name => $val) {
                if (is_bool($val)) {
                    $permission[$module][$name] = $val;
                } elseif (is_string($val)) {
                    $policy = $this->fetchPolicy($val);
                    $permission[$module][$name] = $policy;
                }
            }

            $this->userModel->unguard();
            $this->user->update($user_id, ['permissions' => json_encode($permission)]);
            $this->userModel->reguard();
        }
        return true;
    }

    /**
     * set permission for given role
     *
     * @param       $role_name
     * @param       $module
     * @param array $abilities
     * @return bool
     */
    public function setRolePermissions($role_name, $module, $abilities = [])
    {
        $role = $this->role->findBy('role_name', $role_name);
        if ($role) {
            $permission = json_to_array($role->permission);
            foreach ($abilities as $name => $val) {
                if (is_bool($val)) {
                    $permission[$module][$name] = $val;
                } elseif (is_string($val)) {
                    $policy = $this->fetchPolicy($val);
                    $permission[$module][$name] = $policy;
                }
            }
            $role->update(['permission' => $permission]);
        } else {
            $row = ['role_name' => $role_name, 'permission' => []];
            foreach ($abilities as $name => $val) {
                if (is_bool($val)) {
                    $row['permission'][$module][$name] = $val;
                } elseif (is_string($val)) {
                    $policy = $this->fetchPolicy($val);
                    $row['permission'][$module][$name] = $policy;
                }
            }
            $this->role->create($row);
        }
        return true;
    }

    /**
     * execute policy method with its parameters
     *
     * @param       $callable
     * @param array $params
     * @return bool|mixed
     */
    protected function callPolicy($callable, $params = [])
    {
        $arr_callable = explode('@', $callable);
        if (count($arr_callable) == 2) {
            if (method_exists($arr_callable[0], $arr_callable[1])) {
                $class = new $arr_callable[0]();
                $method = $arr_callable[1];

                return call_user_func_array([$class, $method], $params);
            }
        }

        return false;
    }

    /**
     * check this permission for the given user is allowed
     *
     * @param       $permission
     * @param       $user
     * @param array $params
     * @return bool|mixed
     * @throws \Exception
     */
    protected function canUserDo($permission, $user, $params = [])
    {
        $parameters = [$user];

        if (is_null($permission)) {
            return false;
        }

        $auth_permission = $this->json->collect($this->abilities)->from($permission)->get();

        foreach ($params as $param) {
            array_push($parameters, $param);
        }

        if (empty($auth_permission)) {
            return false;
        }

        $policies = config('permit.policies', []);
        $policy = $policies[$permission] ?? null;
        if ($policy) {
            return $this->callPolicy($policy, $parameters);
        }

        return true;
    }

    /**
     * check the given users has attest one permission
     *
     * @param array $permissions
     * @param       $user
     * @return bool
     * @throws \Exception
     */
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


            if ($this->canUserDo($permission, $user, $params)) {
                return true;
            }
        }

        return false;
    }


    /**
     * getting all abilities from config
     *
     * @param string $module
     * @return array
     */
    public function getAbilities($module = null)
    {
        $abilities_arr = $this->config->get('permit.abilities');

        $abilities = null;
        if (is_null($module)) {
            $abilities = $abilities_arr;
        }

        if (isset($abilities_arr[$module])) {
            $abilities = $abilities_arr[$module];
        }

        return $abilities;
    }

    /**
     * get all roles
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->role->getRoles();
    }

    /**
     * getting a single role
     *
     * @param $role
     * @return mixed
     */
    public function role($role)
    {
        return $this->role->getRole($role);
    }
}
