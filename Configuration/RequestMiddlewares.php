<?php

declare(strict_types=1);

return [
    'frontend' => [
        'person-api' => [
            'target' => \Pluswerk\BePermissions\Middleware\BePermissionsApiMiddleware::class,
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ],
            'after' => [
                'typo3/cms-frontend/site'
            ]
        ]
    ]
];
