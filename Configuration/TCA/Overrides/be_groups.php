<?php

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
                'items' => \Pluswerk\BePermissions\Value\DeployProcessing::tcaItems(),
                'default' => (string)\Pluswerk\BePermissions\Value\DeployProcessing::createWithDefault()
            ],
        ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups', $fields);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('be_groups', 'identifier,code_managed_group,deploy_processing', '', 'after:title');
});
