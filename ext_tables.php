<?php

declare(strict_types=1);

use SebastianHofer\BePermissions\Controller\Backend\BeGroupsCompareModuleController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

call_user_func(function () {
    ExtensionManagementUtility::addModule(
        'system',
        'bepermissions',
        '',
        '',
        [
            'routeTarget' => BeGroupsCompareModuleController::class . '::handleRequest',
            'access' => 'admin',
            'name' => 'system_bepermissions',
            'icon' => 'EXT:be_permissions/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:be_permissions/Resources/Private/Language/locallang_mod.xlf',
        ]
    );
});
