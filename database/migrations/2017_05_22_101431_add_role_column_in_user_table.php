<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleColumnInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn(config('permit.users.table'), config('permit.users.role_column'))) {
            Schema::table(config('permit.users.table'), function(Blueprint $tbl) {
                $tbl->string(config('permit.users.role_column'), 50);
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
        if (Schema::hasColumn(config('permit.users.table'), config('permit.users.role_column'))) {
            Schema::table(config('permit.users.table'), function(Blueprint $tbl) {
                $tbl->dropColumn(config('permit.users.role_column'));
            });
        }
    }
}
