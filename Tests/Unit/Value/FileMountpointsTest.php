<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractIntArrayField;
use Pluswerk\BePermissions\Value\FileMountpoints;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\FileMountpoints
 */
final class FileMountpointsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_file_mountpoints(): void
    {
        $pageTypesSelect = FileMountpoints::createFromYamlConfiguration([]);
        $this->assertSame('file_mountpoints', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, FileMountpoints::createFromYamlConfiguration([]));
        $this->assertInstanceOf(AbstractIntArrayField::class, FileMountpoints::createFromDBValue(''));
    }
}
