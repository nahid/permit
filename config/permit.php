<?php

return [
    'users' => [
        'model' => \App\User::class,
        'table' => 'users',
        'role_column'   => 'role'
    ],
    'super_user'    =>  'admin',

    'permissions'   => [
        "post"  => ['create', 'update', 'delete'],
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
    ]

];