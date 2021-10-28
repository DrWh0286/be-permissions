<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractIntArrayField;
use Pluswerk\BePermissions\Value\CategoryPerms;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Value\CategoryPerms
 */
final class CategoryPermsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function field_name_is_category_perms(): void
    {
        $pageTypesSelect = CategoryPerms::createFromYamlConfiguration([]);
        $this->assertSame('category_perms', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, CategoryPerms::createFromYamlConfiguration([]));
        $this->assertInstanceOf(AbstractIntArrayField::class, CategoryPerms::createFromDBValue(''));
    }

}
