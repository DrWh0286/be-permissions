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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    $fields = [
        'identifier' => [
            'label' => 'configuration identifier',
            'config' => [
                'type' => 'user',
                'renderType' => 'identifierField'
            ]
        ],
        'code_managed_group' => [
            'label' => 'Code Managed Group',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ]
        ],
        'deploy_processing' => [
            'label' => 'Deploy Processing',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \SebastianHofer\BePermissions\Value\DeployProcessing::tcaItems(),
                'default' => (string)\SebastianHofer\BePermissions\Value\DeployProcessing::createWithDefault()
            ],
        ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups', $fields);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('be_groups', 'identifier,code_managed_group,deploy_processing', '', 'after:title');
});
