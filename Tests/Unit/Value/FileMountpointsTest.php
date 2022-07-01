<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\AbstractIntArrayField;
use SebastianHofer\BePermissions\Value\FileMountpoints;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\FileMountpoints
 */
final class FileMountpointsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_file_mountpoints(): void //phpcs:ignore
    {
        $pageTypesSelect = FileMountpoints::createFromYamlConfiguration([]);
        $this->assertSame('file_mountpoints', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void //phpcs:ignore
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, FileMountpoints::createFromYamlConfiguration([]));
        $this->assertInstanceOf(AbstractIntArrayField::class, FileMountpoints::createFromDBValue(''));
    }
}
