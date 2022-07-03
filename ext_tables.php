<?php

defined('TYPO3') or die();

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'BePermissions',
        'system',
        'BeGroupsCompare',
        'top',
        [
            \SebastianHofer\BePermissions\Controller\Backend\BeGroupsCompareModuleController::class => 'index,detail'
            ],
        [
            'access' => 'admin',
            'icon' => 'EXT:be_permissions/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:be_permissions/Resources/Private/Language/locallang_mod.xlf',
            ]
    );
});
