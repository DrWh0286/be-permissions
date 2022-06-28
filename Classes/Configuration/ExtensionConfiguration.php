<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\AvailableWidgets;
use Pluswerk\BePermissions\Value\BulkExport;
use Pluswerk\BePermissions\Value\CategoryPerms;
use Pluswerk\BePermissions\Value\DbMountpoints;
use Pluswerk\BePermissions\Value\DeployProcessing;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\FileMountpoints;
use Pluswerk\BePermissions\Value\FilePermissions;
use Pluswerk\BePermissions\Value\GroupMods;
use Pluswerk\BePermissions\Value\MfaProviders;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\PageTypesSelect;
use Pluswerk\BePermissions\Value\Source;
use Pluswerk\BePermissions\Value\TablesModify;
use Pluswerk\BePermissions\Value\TablesSelect;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as T3ExtensionConfiguration;

final class ExtensionConfiguration implements SingletonInterface, ExtensionConfigurationInterface
{
    /** @var array|string[][][] */
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
            'bulk_export' => BulkExport::class
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

        return is_string($config['valueObjectMapping'][$fieldName]) ? $config['valueObjectMapping'][$fieldName] : '';
    }

    public function getProductionHost(): string
    {
        return $this->getHostBySource(new Source('production'));
    }

    public function getApiToken(): string
    {
        $config = $this->getConfig();

        return (isset($config['apiToken']) && is_string($config['apiToken'])) ? $config['apiToken'] : '';
    }

    public function getHostBySource(Source $source): string
    {
        $config = $this->getConfig();

        $host = (isset($config['environments'][(string)$source]['host']) && is_string($config['environments'][(string)$source]['host']))
            ? $config['environments'][(string)$source]['host']
            : '';

        if (!is_string($host)) {
            throw new \RuntimeException($source . ' host must be a string!');
        }

        return $host;
    }

    /**
     * @return array|string[][][]
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private function getConfig(): array
    {
        if (empty($this->config)) {
            /** @var T3ExtensionConfiguration $config */
            $config = GeneralUtility::makeInstance(T3ExtensionConfiguration::class);
            /** @var array|string[][][] $tmpConfig */
            $tmpConfig = (array)$config->get('be_permissions');
            /** @var array|string[][][] $resultingConfig */
            $resultingConfig = array_replace_recursive($this->baseConfig, $tmpConfig);
            $this->config = $resultingConfig;
        }

        return $this->config;
    }
}
