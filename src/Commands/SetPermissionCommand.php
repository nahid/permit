<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Roles\RoleRepository;
use Nahid\Permit\Users\UserRepository;

class SetPermissionCommand extends Command
{

    /**
     * @var RoleRepository
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
    protected $signature = 'permit:set {type : two types 1. user 2. role} {needle : desire permission entity} {permission : permission name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission to user or role';


    /**
     * AddPermissionCommand constructor.
     *
     * @param RoleRepository $permissionRepository
     * @param UserRepository       $userRepository
     */
    public function __construct(RoleRepository $permissionRepository, UserRepository $userRepository)
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
            return $this->setUserPermission();
        }

        if ($type == 'role') {
            return $this->setRolePermission();
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
        return $ability;
    }

    /**
     * add new ability to an user
     *
     * @return bool
     */
    public function setUserPermission()
    {
        $expected_values = ['true'=>true, 'false'=>false];
        $user_id = $this->argument('needle');
        $prm = $this->argument('permission');
        $explode = explode('=', $prm);
        $ability = true;

        if (count($explode) == 2) {
            $prm = $explode[0];
            if (isset($expected_values[$explode[1]])) {
                $ability = $expected_values[$explode[1]];
            } else {
                $ability = $this->fetchPolicy($explode[1]);
            }

        }

        $prms = explode('.', $prm);
        $abilities = config('permit.abilities');

        $module = $prms[0];
        if (!isset($abilities[$module])) {
            $this->error('No modules are defined in config');
            return false;
        }

        $mod_perms = $abilities[$module];
        $user = $this->user->find($user_id);

        if ($user) {
            $permission = json_to_array($user->permissions);
            if (!in_array($prms[1], $mod_perms) && !array_key_exists($prms[1], $mod_perms)) {
                $this->error('Please set this permission in config/permit.php first!');
                return false;
            }

            $permission[$module][$prms[1]] = $ability;
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
    public function setRolePermission()
    {
        $expected_values = ['true'=>true, 'false'=>false];
        $role_name = $this->argument('needle');
        $prm = $this->argument('permission');
        $abilities = config('permit.abilities');
        $explode = explode('=', $prm);
        $ability = true;

        if (count($explode) == 2) {
            $prm = $explode[0];
            if (isset($expected_values[$explode[1]])) {
                $ability = $expected_values[$explode[1]];
            } else {
                $ability = $this->fetchPolicy($explode[1]);
            }

        }

        $prms = explode('.', $prm);

        $module = $prms[0];
        if (!isset($abilities[$module])) {
            $this->error('No modules are defined in config');
            return false;
        }

        $mod_perms = $abilities[$module];
        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_to_array($role->permission);
            if (!in_array($prms[1], $mod_perms) && !array_key_exists($prms[1], $mod_perms)) {
                $this->error('Please set this permission in config/permit.php first!');
                return false;
            }

            $permission[$module][$prms[1]] = $ability;
            $this->permission->update($role->id, ['permission' => json_encode($permission)]);
            $this->info('Successfully added permission to role');
            return true;
        }

        $this->error('No role found');
        return false;
    }
}
