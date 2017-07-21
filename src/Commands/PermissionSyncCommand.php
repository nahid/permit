<?php

namespace Nahid\Permit\Commands;

use Illuminate\Console\Command;

class PermissionSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permit:permissions {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions to dababase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info($this->argument('cmd'));
    }
}
