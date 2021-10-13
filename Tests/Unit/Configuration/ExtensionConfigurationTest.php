<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Configuration\NoValueObjectConfiguredException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\ExtensionConfiguration
 */
final class ExtensionConfigurationTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [
            'valueObjectMapping' => [
                'non_exclude_fields' => 'Pluswerk\BePermissions\Value\NonExcludeFields'
            ]
        ];
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
}
