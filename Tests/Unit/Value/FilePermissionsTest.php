<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractStringArrayField;
use Pluswerk\BePermissions\Value\FilePermissions;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\FilePermissions
 */
final class FilePermissionsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_file_permissions(): void
    {
        $filePermissions = FilePermissions::createFromYamlConfiguration([]);
        $this->assertSame('file_permissions', $filePermissions->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractStringArrayField(): void
    {
        $filePermissions = FilePermissions::createFromYamlConfiguration([]);
        $this->assertInstanceOf(AbstractStringArrayField::class, $filePermissions);
    }

}
