<?php

return [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'users' => [
        'model' => \App\Models\User::class,
        'table' => 'users',
    ],

    'roles_table' => 'roles',
    'user_roles_table' => 'user_roles',
    
    'debug' => [
        'superuser_mode' => false,
    ],

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
