<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\AvailableWidgets;
use Pluswerk\BePermissions\Value\CategoryPerms;
use Pluswerk\BePermissions\Value\DbMountpoints;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\FileMountpoints;
use Pluswerk\BePermissions\Value\FilePermissions;
use Pluswerk\BePermissions\Value\GroupMods;
use Pluswerk\BePermissions\Value\LockToDomain;
use Pluswerk\BePermissions\Value\MfaProviders;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\PageTypesSelect;
use Pluswerk\BePermissions\Value\TablesModify;
use Pluswerk\BePermissions\Value\TablesSelect;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as T3ExtensionConfiguration;

final class ExtensionConfiguration implements SingletonInterface, ExtensionConfigurationInterface
{
    /** @var array<array, string>|null */
    private ?array $config = null;

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
            'file_mountpoint' => FileMountpoints::class,
            'category_perms' => CategoryPerms::class,
            'groupMods' => GroupMods::class,
            'file_permissions' => FilePermissions::class,
            'availableWidgets' => AvailableWidgets::class,
            'mfa_providers' => MfaProviders::class
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

    /**
     * @return array<array, string>
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private function getConfig(): array
    {
        if ($this->config === null) {
            /** @var T3ExtensionConfiguration $config */
            $config = GeneralUtility::makeInstance(T3ExtensionConfiguration::class);
            $tmpConfig = $config->get('be_permissions');
            /** @var array<array, string> $resultingConfig */
            $resultingConfig = array_replace_recursive($this->baseConfig, $tmpConfig);
            $this->config = $resultingConfig;
        }

        return $this->config;
    }
}
