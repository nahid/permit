<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;
use Nahid\Permit\Permissions\PermissionRepository;


class CreateRoleCommand extends Command
{

    protected $permission;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permit:role {action : two type action 1. create 2. delete } {name : name of permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or delete role';



    /**
     * Create a new command instance.
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        parent::__construct();

        $this->permission = $permissionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $action = $this->argument('action');
        $role = $this->permission->findBy('role_name', $name);

        if (!$this->confirm('Do you want to ' . $action . ' role?')) {
            $this->error("Action cancelled");
            return false;
        }

        if ($action == 'create') {
            if (!$role) {
                $this->permission->create(['role_name'=>$name, 'permission'=>"{}"]);
                $this->info('Successfully created new role '. $name);
                return true;
            }
            $this->error($name. ' is already exists');
            return true;
        }

        if ($action == 'delete') {
            if ($role) {
                $role->delete();
                $this->info('Successfully deleted role '. $name);
                return true;
            }
            $this->error('No role found!');
            return true;
        }

        $this->error('Bad arguments');
    }


}
