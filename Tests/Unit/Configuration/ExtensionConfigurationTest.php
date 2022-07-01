<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\AvailableWidgets;
use Pluswerk\BePermissions\Value\CodeManagedGroup;
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
use Pluswerk\BePermissions\Value\TablesModify;
use Pluswerk\BePermissions\Value\TablesSelect;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\ExtensionConfiguration
 */
final class ExtensionConfigurationTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function value_object_class_name_can_be_fetched_by_db_field_name(): void //phpcs:ignore
    {
        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertSame('Pluswerk\BePermissions\Value\NonExcludeFields', $extensionConfiguration->getClassNameByFieldName('non_exclude_fields'));
    }

    /**
     * @test
     */
    public function an_exception_is_thrown_if_no_value_object_is_configured(): void //phpcs:ignore
    {
        $extensionConfiguration = new ExtensionConfiguration();

        $this->expectException(NoValueObjectConfiguredException::class);
        $extensionConfiguration->getClassNameByFieldName('not_configured');
    }

    /**
     * @test
     */
    public function the_configuration_can_be_overwritten(): void //phpcs:ignore
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [
            'valueObjectMapping' => [
                'non_exclude_fields' => 'OverrideClass'
            ]
        ];

        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertSame('OverrideClass', $extensionConfiguration->getClassNameByFieldName('non_exclude_fields'));
    }

    /**
     * @test
     */
    public function default_configuration_is_set_for_value_object_mapping(): void //phpcs:ignore
    {
        $valueObjectMapping = [
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
        ];

        $extensionConfiguration = new ExtensionConfiguration();

        foreach ($valueObjectMapping as $fieldName => $mappingEntry) {
            $this->assertSame($mappingEntry, $extensionConfiguration->getClassNameByFieldName($fieldName));
        }
    }

    /**
     * @test
     */
    public function default_api_token_is_empty_string(): void //phpcs:ignore
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];

        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertSame('', $extensionConfiguration->getApiToken());
    }

    /**
     * @test
     */
    public function configured_api_token_is_returned(): void //phpcs:ignore
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [
            'apiToken' => 'thisisadummyapitokenfortesting'
        ];

        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertSame('thisisadummyapitokenfortesting', $extensionConfiguration->getApiToken());
    }

    /**
     * @test
     * @dataProvider apiUriProvider
     *
     * @param string[][] $extConf
     */
    public function api_base_uri_is_returned(array $extConf, Uri $expectedUri): void //phpcs:ignore
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = $extConf;

        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertEquals($expectedUri, $extensionConfiguration->getApiUri());
    }

    /**
     * @return array<string, array<string, array<string, string>|Uri>>
     */
    public function apiUriProvider(): array
    {
        return [
            'api uri without basic auth' => [
                'extConf' => [
                    'productionHost' => 'https://production.host',
                    'basicAuthUser' => '',
                    'basicAuthPassword' => ''
                ],
                'expectedUri' => new Uri('https://production.host')
            ],
            'api uri with basic auth' => [
                'extConf' => [
                    'productionHost' => 'https://production.host',
                    'basicAuthUser' => 'user',
                    'basicAuthPassword' => 'password'
                ],
                'expectedUri' => new Uri('https://user:password@production.host')
            ]
        ];
    }
}
