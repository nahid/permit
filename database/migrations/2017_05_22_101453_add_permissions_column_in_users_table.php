<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsColumnInUsersTable extends Migration
{
    public function __construct()
    {
        $this->connection = config("permit.connection");
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection($this->connection)->hasColumn(config('permit.users.table'), 'permissions')) {
            Schema::connection($this->connection)->table(config('permit.users.table'), function(Blueprint $tbl) {
                $tbl->text('permissions')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::connection($this->connection)->hasColumn(config('permit.users.table'), 'permissions')) {
            Schema::connection($this->connection)->table(config('permit.users.table'), function(Blueprint $tbl) {
                $tbl->dropColumn('permissions');
            });
        }
    }
}
