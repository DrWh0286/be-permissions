<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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
