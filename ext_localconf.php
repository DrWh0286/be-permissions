<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['be_permissions'] = \SebastianHofer\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['bepermissions_apiroutes'] ??= [];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656345040] = [
        'nodeName' => 'identifierField',
        'priority' => 40,
        'class' => \SebastianHofer\BePermissions\Form\Element\IdentifierField::class,
    ];
});
