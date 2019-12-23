<?php

return [
    'users' => [
        'model' => \App\User::class,
        'table' => 'users',
        'role_column'   => 'type'
    ],

    'roles_table' => 'permissions',
    'user_roles_table' => 'user_permissions',

    'super_user'    =>  'superadmin',

    'abilities'   => [
        /*"module"  => ['create', 'update', 'delete'],*/
    ],


    'policies'  => [
        /*'module' => [
            'update'    => '\App\Permit\Policies\PostPolicy@update',
        ],*/
    ],



    'roles' => [
        /*'role_name' => [
            'ability.*',
        ],*/
    ]


];
