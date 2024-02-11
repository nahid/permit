<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Roles\RoleRepository;
use Nahid\Permit\Users\UserRepository;
use Nahid\JsonQ\Jsonq;

class PermissionSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permit:sync {--Y|yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions to database';


    /**
     * @var mixed
     */
    protected $abilities;

    /**
     * @var mixed
     */
    protected $roles;

    /**
     * @var mixed
     */
    protected $policies;

    /**
     * @var mixed
     */
    protected $roleColumn;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var RoleRepository
     */
    protected $permission;

    /**
     * @var
     */
    protected $userColumn;


    /**
     * @var mixed
     */
    protected $superUser;


    /**
     * PermissionSyncCommand constructor.
     *
     * @param UserRepository       $userRepository
     * @param RoleRepository $permissionRepository
     */
    public function __construct(UserRepository $userRepository, RoleRepository $permissionRepository)
    {
        parent::__construct();
        $this->abilities = config('permit.abilities');
        $this->roles = config('permit.roles');
        $this->policies = config('permit.policies');
        $this->roleColumn = config('permit.users.role_column');
        $this->superUser = config('permit.super_user');
        $this->user = $userRepository;
        $this->permission = $permissionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->syncRolePermissions();
    }

    /**
     * sync local role permissions to database
     */
    protected function syncRolePermissions()
    {
        $data = [];
        $jsonq = new Jsonq();
        $permission_object = $jsonq->collect($this->abilities);
        foreach ($this->roles as $role=>$permission) {
            $permissions = [];
            foreach ($permission as $rules) {
                $rule = explode('.', $rules);
                $perms = $permission_object->reset($this->abilities)->from($rule[0])->get(true);
                if ($rule[1] == '*') {
                    if ($perms) {
                        if (!isset($permissions[$rule[0]])) {
                            $permissions[$rule[0]] = [];
                        }

                        $auth_perms = [];
                        foreach ($perms as $perm => $permission) {
                            if (is_int($perm)) {
                                $auth_perms[$permission] = true;
                            } elseif (is_string($permission)) {
                                $auth_perms[$perm] = $permission;
                            }
                        }
                        $permissions[$rule[0]] = $auth_perms;
                    }
                } else {
                    if ($perms) {
                        if (!isset($permissions[$rule[0]])) {
                            $permissions[$rule[0]] = [];
                        }

                        if (in_array($rule[1], $perms)) {
                            $permissions[$rule[0]][$rule[1]] = true;
                        } elseif (array_key_exists($rule[1], $perms)) {
                            $permissions[$rule[0]][$rule[1]] = $perms[$rule[1]];
                        }
                    }
                }
            }

            $data[] = ['role_name'=>$role, 'permission'=>json_encode($permissions)];
        }

        $roles = $this->getUnusedRoles();
        $opt = $this->option("yes");

        if (is_array($data)) {
            if ($opt) {
                $this->migrate($data, $roles);
            }else {
                if ($this->confirm('Do you wish to sync with existing permissions?')) {
                    $this->migrate($data, $roles);
                } else {
                    $this->error('Process Canceled!');
                }
            }

        }
    }

    /**
     * migrate roles to database
     *
     * @param $data
     * @param $roles
     */
    protected function migrate($data, $roles)
    {
        $db = app('db');
        $db->beginTransaction();
        foreach ($data as $d) {
            if (!$this->permission->syncRolePermissions($d['role_name'], $d, $roles)) {
                $db->rollback();
                $this->error('Roles Synced Failed!');
                return false;

            }
        }
        $db->commit();
        $this->info('Roles Synced!');
    }

    /**
     * get all unused roles
     *
     * @return array
     */
    protected function getUnusedRoles()
    {
        $current_roles = $this->permission->getRoles()->pluck(['role_name'])->toArray();
        $new_roles = array_keys($this->roles);
        $roles = [];

        foreach ($current_roles as $role) {
            if (!in_array($role, $new_roles)) {
                $roles[] = $role;
            }
        }

        return $roles;
    }
}
