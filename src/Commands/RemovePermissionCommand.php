<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Permissions\PermissionRepository;
use Nahid\Permit\Users\UserRepository;

class RemovePermissionCommand extends Command
{

    protected $permission;
    protected $user;
    protected $userModel;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
//    protected $signature = 'permit:user {id} {action} {permission}';
    protected $signature = 'permit:remove {type : two types 1. user 2. role} {needle : desire permission entity} {permission : permission name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove user or role permission';



    /**
     * Create a new command instance.
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



    public function removeUserPermission()
    {
        $user_id = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);

        $module = $prms[0];
        $user = $this->user->find($user_id);

        if ($user) {
            $permission = json_decode($user->permissions, true);
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


    public function removeRolePermission()
    {
        $role_name = $this->argument('needle');
        $prm = $this->argument('permission');
        $prms = explode('.', $prm);

        $module = $prms[0];
        $role = $this->permission->findBy('role_name', $role_name);

        if ($role) {
            $permission = json_decode($role->permission, true);
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
