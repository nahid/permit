<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Roles\RoleRepository;
use Nahid\Permit\Users\UserRepository;

class RemovePermissionCommand extends Command
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
     * @var
     */
    protected $userModel;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permit:remove {type : two types 1. user 2. role} {needle : desire permission entity} {permission : permission name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove user or role permission';


    /**
     * RemovePermissionCommand constructor.
     *
     * @param RoleRepository $permissionRepository
     * @param UserRepository       $userRepository
     */
    public function __construct(RoleRepository $permissionRepository, UserRepository $userRepository)
    {
        parent::__construct();
        $namespace = config('permit.users.model');
        if (class_exists($namespace)) {
            $this->userModel = new $namespace();
        }
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

        if (!$this->confirm('Do you want to remove ' . $type. ' permission?')) {
            $this->error("Action cancelled");
            return false;
        }

        if ($type == 'user') {
            return $this->removeUserPermission();
        }

        if ($type == 'role') {
            return $this->removeRolePermission();
        }

        $this->error('Bad parameters');

        return true;
    }


    /**
     * remove permission from an user
     *
     * @return bool
     */
    public function removeUserPermission()
    {
        $user_id = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);

        $module = $prms[0];
        $user = $this->user->find($user_id);

        if ($user) {
            $permission = json_to_array($user->permissions);
            if (isset($permission[$module][$prms[1]])) {
                unset($permission[$module][$prms[1]]);
            }

            $this->user->update($user->id, ['permissions' => json_encode($permission)]);

            $this->info('Successfully added permission to user');
            return true;
        }

        $this->error('No user found');
        return false;
    }


    /**
     * remove permission from a role
     *
     * @return bool
     */
    public function removeRolePermission()
    {
        $role_name = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);

        $module = $prms[0];
        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_to_array($role->permission);
            if (isset($permission[$module][$prms[1]])) {
                unset($permission[$module][$prms[1]]);
            }

            $this->permission->update($role->id, ['permission' => json_encode($permission)]);

            $this->info('Successfully added permission to role');
            return true;
        }

        $this->error('No role found');
        return false;
    }
}
