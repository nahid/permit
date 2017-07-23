<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn(config('permit.users.table'), 'permissions')) {
            Schema::table(config('permit.users.table'), function(Blueprint $tbl) {
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
        if (!Schema::hasColumn(config('permit.users.table'), 'permissions')) {
            Schema::table(config('permit.users.table'), function(Blueprint $tbl) {
                $tbl->dropColumn('permissions');
            });
        }
    }
}
