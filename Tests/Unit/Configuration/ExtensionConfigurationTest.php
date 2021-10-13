<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\DbMountpoints;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\TablesSelect;
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
    public function value_object_class_name_can_be_fetched_by_db_field_name(): void
    {
        $extensionConfiguration = new ExtensionConfiguration();

        $this->assertSame('Pluswerk\BePermissions\Value\NonExcludeFields', $extensionConfiguration->getClassNameByFieldName('non_exclude_fields'));
    }

    /**
     * @test
     */
    public function an_exception_is_thrown_if_no_value_object_is_configured(): void
    {
        $extensionConfiguration = new ExtensionConfiguration();

        $this->expectException(NoValueObjectConfiguredException::class);
        $extensionConfiguration->getClassNameByFieldName('not_configured');
    }

    /**
     * @test
     */
    public function the_configuration_can_be_overwritten(): void
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
    public function default_configuration_is_set_for_value_object_mapping(): void
    {
        $valueObjectMapping = [
            'non_exclude_fields' => NonExcludeFields::class,
            'allowed_languages' => AllowedLanguages::class,
            'db_mountpoints' => DbMountpoints::class,
            'explicit_allowdeny' => ExplicitAllowDeny::class,
            'tables_select' => TablesSelect::class,
        ];

        $extensionConfiguration = new ExtensionConfiguration();

        foreach ($valueObjectMapping as $fieldName => $mappingEntry) {
            $this->assertSame($mappingEntry, $extensionConfiguration->getClassNameByFieldName($fieldName));
        }
    }
}
