<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('permit.roles_table', 'roles'), function(Blueprint $tbl) {
            $tbl->increments('id');
            $tbl->string('role_name', 50)->index();
            $tbl->text('permission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('permit.roles_table', 'roles'));
    }
}
