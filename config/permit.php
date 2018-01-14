<?php

return [
    'users' => [
        'model' => \App\User::class,
        'table' => 'users',
        'role_column'   => 'role'
    ],
    'super_user'    =>  'admin',

    'permissions'   => [
        "post"  => ['create', 'update'=>'post.update', 'delete'],
        "user"  => ['create', 'update', 'delete'],
    ],

    'roles' => [
        'manager' => [
            'post.*'
        ],
        'supervisor'    => [
            'post.create',
            'post.update',
            'user.create',
            'user.update',
        ]
    ],

    'policies'  => [
        'post'  => [
            'update'    => '\App\Policies\PostPolicy@update'
        ]
    ]

];