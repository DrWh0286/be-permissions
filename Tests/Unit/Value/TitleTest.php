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

use SebastianHofer\BePermissions\Value\AbstractStringField;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\Title
 */
final class TitleTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void //phpcs:ignore
    {
        $dbValue = 'Group title';

        $title = Title::createFromDBValue($dbValue);

        $this->assertSame($dbValue, $title->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void //phpcs:ignore
    {
        $yamlValue = 'yaml group title';

        $title = Title::createFromYamlConfiguration($yamlValue);

        $this->assertSame($yamlValue, (string)$title);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_title_is_returned(): void //phpcs:ignore
    {
        $title = Title::createFromDBValue('');

        $this->assertSame('', $title->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_title(): void //phpcs:ignore
    {
        $title = Title::createFromYamlConfiguration('');
        $this->assertSame('title', $title->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractStringField(): void //phpcs:ignore
    {
        $title = Title::createFromYamlConfiguration('');
        $this->assertInstanceOf(AbstractStringField::class, $title);
    }

    /**
     * @test
     */
    public function title_is_not_changed_by_extend(): void //phpcs:ignore
    {
        $title = Title::createFromDBValue('Title A');
        $extendTitle = Title::createFromYamlConfiguration('Title B');

        $actualTitle = $title->extend($extendTitle);

        $this->assertSame('Title A', (string)$actualTitle);
        $this->assertNotSame($title, $actualTitle);
        $this->assertNotSame($extendTitle, $actualTitle);
    }
}
