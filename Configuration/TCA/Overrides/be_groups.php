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

    if (
        isset($GLOBALS['TCA']['be_groups']['columns'])
        && is_array($GLOBALS['TCA']['be_groups']['columns'])
        && \TYPO3\CMS\Core\Core\Environment::getContext()->isProduction()
    ) {
        $displayCond = [
            'OR' => [
                'FIELD:code_managed_group:REQ:false',
                'FIELD:deploy_processing:!=:' . (string)\SebastianHofer\BePermissions\Value\DeployProcessing::createOverrule()
            ]
        ];

        $fieldsToShowReadonly = [
            'code_managed_group',
            'deploy_processing'
        ];

        $fieldsToIgnore = [
            'title'
        ];

        foreach ($GLOBALS['TCA']['be_groups']['columns'] as $column => $config) {
            if (in_array($column, $fieldsToIgnore)) {
                continue;
            }

            if (in_array($column, $fieldsToShowReadonly)) {
                $GLOBALS['TCA']['be_groups']['columns'][$column]['config']['readOnly'] = true;
            } else {
                $GLOBALS['TCA']['be_groups']['columns'][$column]['displayCond'] = $displayCond;
            }
        }
    }
});
