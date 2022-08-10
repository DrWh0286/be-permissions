<?php

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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
    'version' => '0.7.1',
    'autoload' => [
        'psr-4' => [
            'SebastianHofer\\BePermissions\\' => 'Classes/',
        ],
    ],
];
