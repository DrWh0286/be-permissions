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

use SebastianHofer\BePermissions\Value\AbstractStringArrayField;
use SebastianHofer\BePermissions\Value\AvailableWidgets;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\AvailableWidgets
 */
final class AvailableWidgetsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 't3news,sysLogErrors';

        $availableWidgets = AvailableWidgets::createFromDBValue($dbValue);

        $this->assertSame(
            ['sysLogErrors', 't3news'],
            $availableWidgets->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = ['t3news', 'sysLogErrors'];

        $availableWidgets = AvailableWidgets::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'sysLogErrors,t3news',
            (string)$availableWidgets
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_available_widgets_array_is_returned(): void //phpcs:ignore
    {
        $availableWidgets = AvailableWidgets::createFromDBValue('');

        $this->assertSame([], $availableWidgets->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_available_widgets_object(): void //phpcs:ignore
    {
        $confArray = ['t3news', 'sysLogErrors'];

        $tablesSelect = AvailableWidgets::createFromYamlConfiguration($confArray);
        $extendTablesSelect = AvailableWidgets::createFromYamlConfiguration(['t3news','docTypoScriptReference']);

        $this->assertSame(
            ['docTypoScriptReference', 'sysLogErrors', 't3news'],
            $tablesSelect->extend($extendTablesSelect)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_availableWidgets(): void //phpcs:ignore
    {
        $tablesSelect = AvailableWidgets::createFromYamlConfiguration([]);
        $this->assertSame('availableWidgets', $tablesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractStringArrayField(): void //phpcs:ignore
    {
        $tablesSelect = AvailableWidgets::createFromYamlConfiguration([]);
        $this->assertInstanceOf(AbstractStringArrayField::class, $tablesSelect);
    }
}
