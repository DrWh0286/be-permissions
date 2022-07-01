<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\AbstractStringArrayField;
use SebastianHofer\BePermissions\Value\FilePermissions;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\FilePermissions
 */
final class FilePermissionsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_file_permissions(): void //phpcs:ignore
    {
        $filePermissions = FilePermissions::createFromYamlConfiguration([]);
        $this->assertSame('file_permissions', $filePermissions->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractStringArrayField(): void //phpcs:ignore
    {
        $filePermissions = FilePermissions::createFromYamlConfiguration([]);
        $this->assertInstanceOf(AbstractStringArrayField::class, $filePermissions);
    }
}
