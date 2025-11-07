
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
        'api' => [
            'driver' => 'session',
            'provider' => 'lydopers',
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'scholars',
        ],
    ],

    'providers' => [
        'lydopers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Lydopers::class,
        ],
        'scholars' => [
            'driver' => 'eloquent',
            'model' => App\Models\Scholar::class,
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