<?php

return [
    'users' => [
        'model' => \App\User::class,
        'table' => 'users',
        'role_column'   => 'type'
    ],

    'super_user'    =>  'admin',

    'abilities'   => [
       // "module"  => ['ability1', 'ability2', 'ability3'=>'policy_module.policy'],
    ],


    'policies'  => [
        /*'module' => [
            'update'    => '\App\Permit\Policies\PostPolicy@update',
        ],*/
    ],



    'roles' => [
        /*'role_name' => [
            'module.ability',
        ],*/
    ]


];