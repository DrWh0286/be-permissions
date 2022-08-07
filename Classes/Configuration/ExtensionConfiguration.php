<?php

declare(strict_types=1);

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

namespace SebastianHofer\BePermissions\Configuration;

use SebastianHofer\BePermissions\Value\AllowedLanguages;
use SebastianHofer\BePermissions\Value\AvailableWidgets;
use SebastianHofer\BePermissions\Value\CodeManagedGroup;
use SebastianHofer\BePermissions\Value\CategoryPerms;
use SebastianHofer\BePermissions\Value\DbMountpoints;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;
use SebastianHofer\BePermissions\Value\FileMountpoints;
use SebastianHofer\BePermissions\Value\FilePermissions;
use SebastianHofer\BePermissions\Value\GroupMods;
use SebastianHofer\BePermissions\Value\MfaProviders;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\PageTypesSelect;
use SebastianHofer\BePermissions\Value\TablesModify;
use SebastianHofer\BePermissions\Value\TablesSelect;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as T3ExtensionConfiguration;

final class ExtensionConfiguration implements SingletonInterface, ExtensionConfigurationInterface
{
    /** @var array|string[][] */
    private array $config = [];

    /** @var array|string[][] */
    private array $baseConfig = [
        'valueObjectMapping' => [
            'non_exclude_fields' => NonExcludeFields::class,
            'allowed_languages' => AllowedLanguages::class,
            'db_mountpoints' => DbMountpoints::class,
            'explicit_allowdeny' => ExplicitAllowDeny::class,
            'tables_select' => TablesSelect::class,
            'tables_modify' => TablesModify::class,
            'title' => Title::class,
            'pagetypes_select' => PageTypesSelect::class,
            'file_mountpoints' => FileMountpoints::class,
            'category_perms' => CategoryPerms::class,
            'groupMods' => GroupMods::class,
            'file_permissions' => FilePermissions::class,
            'availableWidgets' => AvailableWidgets::class,
            'mfa_providers' => MfaProviders::class,
            'deploy_processing' => DeployProcessing::class,
            'code_managed_group' => CodeManagedGroup::class
        ]
    ];

    /**
     * @throws NoValueObjectConfiguredException
     */
    public function getClassNameByFieldName(string $fieldName): string
    {
        $config = $this->getConfig();

        if (!isset($config['valueObjectMapping'][$fieldName])) {
            throw new NoValueObjectConfiguredException('For field ' . $fieldName . ' no value object is configured.');
        }

        return $config['valueObjectMapping'][$fieldName];
    }

    public function getApiToken(): string
    {
        $config = $this->getConfig();

        return (isset($config['apiToken']) && is_string($config['apiToken'])) ? $config['apiToken'] : '';
    }


    public function getApiUri(): Uri
    {
        $config = $this->getConfig();

        $host = (isset($config['remoteHost']) && is_string($config['remoteHost'])) ? $config['remoteHost'] : '';

        $uri = new Uri($host);

        if (
            isset($config['basicAuthUser'])
            && !empty($config['basicAuthUser'])
            && is_string($config['basicAuthUser'])
            && isset($config['basicAuthPassword'])
            && !empty($config['basicAuthPassword'])
            && is_string($config['basicAuthPassword'])
        ) {
            $uri = $uri->withUserInfo($config['basicAuthUser'], $config['basicAuthPassword']);
        }

        return $uri;
    }

    /**
     * @return array|string[][]
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private function getConfig(): array
    {
        if (empty($this->config)) {
            /** @var T3ExtensionConfiguration $config */
            $config = GeneralUtility::makeInstance(T3ExtensionConfiguration::class);
            /** @var array|string[][] $tmpConfig */
            $tmpConfig = (array)$config->get('be_permissions');
            /** @var array|string[][] $resultingConfig */
            $resultingConfig = array_replace_recursive($this->baseConfig, $tmpConfig);
            $this->config = $resultingConfig;
        }

        return $this->config;
    }
}
