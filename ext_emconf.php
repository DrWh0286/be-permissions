<?php

/** @phpstan-ignore-next-line */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend Permissions',
    'description' => 'Makes Backend permissions shippable',
    'category' => 'module',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.999'
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Hofer',
    'author_email' => 'sebastian.hofer@s-hofer.de',
    'version' => '0.6.5',
    'autoload' => [
        'psr-4' => [
            'SebastianHofer\\BePermissions\\' => 'Classes/',
        ],
    ],
];
