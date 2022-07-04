<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
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
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Value\PageTypesSelect;

/**
 * @covers \SebastianHofer\BePermissions\Value\PageTypesSelect
 */
final class PageTypesSelectTest extends UnitTestCase
{
    /**
     * @test
     */
    public function no_error_with_empty_database_field(): void //phpcs:ignore
    {
        $pageTypesSelect = PageTypesSelect::createFromDBValue('');
        $this->assertSame([], $pageTypesSelect->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_array(): void //phpcs:ignore
    {
        $dbValue = '1,4,3,254,199';

        $pageTypesSelect = PageTypesSelect::createFromDBValue($dbValue);

        $this->assertSame(
            [1,3,4,199,254],
            $pageTypesSelect->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $yamlValue = [1,4,3,254,199];

        $pageTypesSelect = PageTypesSelect::createFromYamlConfiguration($yamlValue);

        $this->assertSame(
            '1,3,4,199,254',
            (string)$pageTypesSelect
        );
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_non_pagetypes_select_object(): void //phpcs:ignore
    {
        $pageTypesSelect = PageTypesSelect::createFromYamlConfiguration([1,4,3,254,199]);
        $pageTypesSelectExtend = PageTypesSelect::createFromYamlConfiguration([1,50,51]);
        $pageTypesSelectExpected = PageTypesSelect::createFromYamlConfiguration([1,3,4,50,51,199,254]);

        $pageTypesSelectActual = $pageTypesSelect->extend($pageTypesSelectExtend);

        $this->assertEquals($pageTypesSelectExpected, $pageTypesSelectActual);
    }

    /**
     * @test
     */
    public function field_name_is_pagetypes_select(): void //phpcs:ignore
    {
        $pageTypesSelect = PageTypesSelect::createFromYamlConfiguration([]);
        $this->assertSame('pagetypes_select', $pageTypesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractIntArrayField(): void //phpcs:ignore
    {
        $this->assertInstanceOf(AbstractIntArrayField::class, PageTypesSelect::createFromYamlConfiguration([]));
    }
}
