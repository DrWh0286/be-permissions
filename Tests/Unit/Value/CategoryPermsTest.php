<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value;

use SebastianHofer\BePermissions\Value\AbstractIntArrayField;
use SebastianHofer\BePermissions\Value\CategoryPerms;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\CategoryPerms
 */
final class CategoryPermsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_category_perms(): void //phpcs:ignore
    {
        $pageTypesSelect = CategoryPerms::createFromYamlConfiguration([]);
        $this->assertSame('category_perms', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void //phpcs:ignore
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, CategoryPerms::createFromYamlConfiguration([]));
        $this->assertInstanceOf(AbstractIntArrayField::class, CategoryPerms::createFromDBValue(''));
    }
}
