<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    | You can replace default models from this package by models
    | you created.
    |
    */
    'models' => [
        'groups'            => \Devon\AuthPlus\Group::class,
        'group_permissions' => \Devon\AuthPlus\GroupPermission::class,
        'users'             => \App\User::class,
    ],
    'tables' => [
        'groups'            => 'groups',
        'group_permissions' => 'group_permissions',
        'users'             => 'users',
        'group_users'       => 'group_users',
    ],
];