<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\DbMountpoints;

/**
 * @covers \Pluswerk\BePermissions\Value\DbMountpoints
 */
final class DbMountpointsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = '1,2';

        $dbMountpoints = DbMountpoints::createFromDBValue($dbValue);

        $this->assertSame([1,2], $dbMountpoints->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = [1,2];

        $dbMountpoints = DbMountpoints::createFromYamlConfiguration($confArray);

        $this->assertSame('1,2', (string)$dbMountpoints);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void //phpcs:ignore
    {
        $dbMountpoints = DbMountpoints::createFromDBValue('');

        $this->assertSame([], $dbMountpoints->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_db_mountpoints_object(): void //phpcs:ignore
    {
        $confArray = [1,2];

        $dbMountpoints = DbMountpoints::createFromYamlConfiguration($confArray);
        $extendDbMountpoints = DbMountpoints::createFromYamlConfiguration([2,3]);

        $this->assertSame([1,2,3], $dbMountpoints->extend($extendDbMountpoints)->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_db_mountpoints(): void //phpcs:ignore
    {
        $dbMountpoints = DbMountpoints::createFromYamlConfiguration([]);
        $this->assertSame('db_mountpoints', $dbMountpoints->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $dbMountpoints = DbMountpoints::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $dbMountpoints);
    }
}
