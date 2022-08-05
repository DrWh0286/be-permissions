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

defined('TYPO3_MODE') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['be_permissions_identifier'] = \SebastianHofer\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['be_permissions_export'] = \SebastianHofer\BePermissions\Hook\DataHandlerBeGroupsAutomaticExportHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['bepermissions_apiroutes'] ??= [];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656345040] = [
        'nodeName' => 'identifierField',
        'priority' => 40,
        'class' => \SebastianHofer\BePermissions\Form\Element\IdentifierField::class,
    ];

    // Feature Toggles
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['be_permissions.automaticBeGroupsExportWithSave'] ??= false;
});
