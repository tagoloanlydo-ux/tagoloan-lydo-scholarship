<?php
return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'lydopers',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'lydopers',
        ],
    ],

    'providers' => [
        'lydopers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Lydopers::class,
        ],
    ],

    'passwords' => [
        'lydopers' => [
            'provider' => 'lydopers',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],


];
