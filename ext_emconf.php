<?php

/**
 * Extension Manager/Repository config file for ext "higher_education_package".
 */

/** @phpstan-ignore-next-line */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend Permissions',
    'description' => 'Makes Backend permissions shippable',
    'category' => 'module',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-10.5.999'
        ],
    ],
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Hofer',
    'author_email' => 'sebastian.hofer@s-hofer.de',
    'version' => '0.0.1',
];
