<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,

        // View settings
        'view' => [
            'template_path' => [
                __DIR__ . '/../templates',
                __DIR__ . '/../src/Action/AddRef',
                __DIR__ . '/../src/Action/Assign',
                __DIR__ . '/../src/Action/Control',
                __DIR__ . '/../src/Action/EditRef',
                __DIR__ . '/../src/Action/End',
                __DIR__ . '/../src/Action/Full',
                __DIR__ . '/../src/Action/Greet',
                __DIR__ . '/../src/Action/Lock',
                __DIR__ . '/../src/Action/Logon',
                __DIR__ . '/../src/Action/Master',
                __DIR__ . '/../src/Action/Refs',
                __DIR__ . '/../src/Action/TwigTest',
            ],
            'twig' => [
                'cache' => __DIR__ . '/../var/cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        'upload_path' => __DIR__ . '/../var/uploads',
        
        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../var/log/app.log',
        ],

    ],
];
