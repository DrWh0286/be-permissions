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
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void
    {
        $dbValue = '1,2';

        $dbMountpoints = DbMountpoints::createFromDBValue($dbValue);

        $this->assertSame([1,2], $dbMountpoints->asArray());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void
    {
        $confArray = [1,2];

        $dbMountpoints = DbMountpoints::createFromConfigurationArray($confArray);

        $this->assertSame('1,2', (string)$dbMountpoints);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void
    {
        $dbMountpoints = DbMountpoints::createFromDBValue('');

        $this->assertSame([], $dbMountpoints->asArray());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_db_mountpoints_object(): void
    {
        $confArray = [1,2];

        $dbMountpoints = DbMountpoints::createFromConfigurationArray($confArray);
        $extendDbMountpoints = DbMountpoints::createFromConfigurationArray([2,3]);

        $this->assertSame([1,2,3], $dbMountpoints->extend($extendDbMountpoints)->asArray());
    }

    /**
     * @test
     */
    public function field_name_is_db_mountpoints(): void
    {
        $dbMountpoints = DbMountpoints::createFromConfigurationArray([]);
        $this->assertSame('db_mountpoints', $dbMountpoints->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void
    {
        $dbMountpoints = DbMountpoints::createFromConfigurationArray([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $dbMountpoints);
    }
}
