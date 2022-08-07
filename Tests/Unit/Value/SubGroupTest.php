<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Value\SubGroup;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\SubGroup
 */
final class SubGroupTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 'test_group,another_group';

        $tablesSelect = SubGroup::createFromDBValue($dbValue);
        $expected = ['another_group','test_group'];

        $this->assertSame(
            $expected,
            $tablesSelect->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = ['test_group','another_group'];

        $tablesSelect = SubGroup::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'another_group,test_group',
            (string)$tablesSelect
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void //phpcs:ignore
    {
        $tablesSelect = SubGroup::createFromDBValue('');

        $this->assertSame([], $tablesSelect->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_subgroup_object(): void //phpcs:ignore
    {
        $confArray = ['test_group','another_group'];

        $subGroup = SubGroup::createFromYamlConfiguration($confArray);
        $extendSubGroup = SubGroup::createFromYamlConfiguration(
            ['test_group','a_third_group']
        );

        $this->assertSame(
            ['a_third_group','another_group','test_group'],
            $subGroup->extend($extendSubGroup)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_subgroup(): void //phpcs:ignore
    {
        $tablesSelect = SubGroup::createFromYamlConfiguration([]);
        $this->assertSame('subgroup', $tablesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $tablesSelect = SubGroup::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $tablesSelect);
    }
}
