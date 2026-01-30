<?php

return [

    'default' =>'smtp',

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'host' => 'smtp.zoho.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'info@interiorchowk.com',
            'password' => 'LTEbVyYtsmYf',
            'timeout' => null,
            'auth_mode' => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS'),
        'name' => env('MAIL_FROM_NAME'),
    ],

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];