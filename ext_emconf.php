<?php

/**
 * Extension Manager/Repository config file for ext "higher_education_package".
 */

/** @phpstan-ignore-next-line */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Pluswerk Backend Permissions',
    'description' => 'Makes Backend permissions shippable',
    'category' => 'module',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99'
        ],
    ],
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Hofer',
    'author_email' => 'sebastian.hofer@pluswerk.ag',
    'author_company' => 'Pluswerk AG',
    'version' => '0.0.1',
];
