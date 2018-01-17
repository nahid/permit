<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Permissions\PermissionRepository;
use Nahid\Permit\Users\UserRepository;

class AddPermissionCommand extends Command
{

    /**
     * @var PermissionRepository
     */
    protected $permission;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * auth user model
     * @var
     */
    protected $userModel;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permit:add {type : two types 1. user 2. role} {needle : desire permission entity} {permission : permission name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission to user or role';


    /**
     * AddPermissionCommand constructor.
     *
     * @param PermissionRepository $permissionRepository
     * @param UserRepository       $userRepository
     */
    public function __construct(PermissionRepository $permissionRepository, UserRepository $userRepository)
    {
        parent::__construct();
        $namespace = config('permit.users.model');
        $this->userModel = new $namespace();
        $this->user = $userRepository;
        $this->permission = $permissionRepository;
    }

    /**
     * Execute the console command.
     * return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        if (!$this->confirm('Do you want to add ' . $type. ' permission?')) {
            $this->error("Action cancelled");
            return false;
        }

        if ($type == 'user') {
            return $this->addUserPermission();
        }

        if ($type == 'role') {
            return $this->addRolePermission();
        }

        $this->error('Bad parameters');

        return true;
    }


    /**
     * fetch policy by given ability
     *
     * @param $ability
     * @return string
     */
    protected function fetchPolicy($ability)
    {
        $policies = config('permit.policies');
        $policy_str = explode('.', $ability);
        $policy = '';
        if (isset($policies[$policy_str[0]][$policy_str[1]])) {
            $policy = $policies[$policy_str[0]][$policy_str[1]];
        }

        return $policy;
    }

    /**
     * add new ability to an user
     *
     * @return bool
     */
    public function addUserPermission()
    {
        $user_id = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);
        $abilities = config('permit.abilities');

        $module = $prms[0];
        $mod_perms = [];
        if (isset($abilities[$module])) {
            $mod_perms = $abilities[$module];
        }

        $user = $this->user->find($user_id);

        if ($user) {
            $permission = json_decode($user->permissions, true);
            if (in_array($prms[1], $mod_perms)) {
                $permission[$module][$prms[1]] = true;
            } elseif (array_key_exists($prms[1], $mod_perms)) {
                $policy = $this->fetchPolicy($mod_perms[$prms[1]]);
                $permission[$module][$prms[1]] = $policy;
            }

            $this->user->update($user->id, ['permissions' => json_encode($permission)]);
            $this->info('Successfully added permission to user');
            return true;
        }

        $this->error('No user found');
        return false;
    }

    /**
     * add new ability to a role
     *
     * @return bool
     */
    public function addRolePermission()
    {
        $role_name = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);
        $abilities = config('permit.abilities');

        $module = $prms[0];
        $mod_perms = [];
        if (isset($abilities[$module])) {
            $mod_perms = $abilities[$module];
        }

        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_decode($role->permission, true);
            if (in_array($prms[1], $mod_perms)) {
                $permission[$module][$prms[1]] = true;
            } elseif (array_key_exists($prms[1], $mod_perms)) {
                $policy = $this->fetchPolicy($mod_perms[$prms[1]]);
                $permission[$module][$prms[1]] = $policy;
            }

            $this->permission->update($role->id, ['permission' => json_encode($permission)]);
            $this->info('Successfully added permission to role');
            return true;
        }

        $this->error('No role found');
        return false;
    }
}
