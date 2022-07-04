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

use SebastianHofer\BePermissions\Value\AbstractBooleanField;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Value\CodeManagedGroup;

/**
 * @covers \SebastianHofer\BePermissions\Value\CodeManagedGroup
 */
final class CodeManagedGroupTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void // phpcs:ignore
    {
        $dbValue = '1';

        $codeManagedGroup = CodeManagedGroup::createFromDBValue($dbValue);

        $this->assertTrue($codeManagedGroup->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void // phpcs:ignore
    {
        $yamlValue = true;

        $codeManagedGroup = CodeManagedGroup::createFromYamlConfiguration($yamlValue);

        $this->assertEquals('1', (string)$codeManagedGroup);
    }

    /**
     * @test
     */
    public function with_empty_database_field_the_value_is_false(): void // phpcs:ignore
    {
        $codeManagedGroup = CodeManagedGroup::createFromDBValue('');

        $this->assertFalse($codeManagedGroup->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_bulk_export(): void // phpcs:ignore
    {
        $codeManagedGroup = CodeManagedGroup::createFromDBValue('');

        $this->assertSame('code_managed_group', $codeManagedGroup->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractBooleanField(): void // phpcs:ignore
    {
        $codeManagedGroup = CodeManagedGroup::createFromDBValue('');

        $this->assertInstanceOf(AbstractBooleanField::class, $codeManagedGroup);
    }

    /**
     * @test
     */
    public function is_overruled_by_extend(): void // phpcs:ignore
    {
        $yamlValue = true;

        $codeManagedGroup = CodeManagedGroup::createFromYamlConfiguration($yamlValue);
        $extendCodeManagedGroup = CodeManagedGroup::createFromYamlConfiguration(false);

        $resultBulkExport = $codeManagedGroup->extend($extendCodeManagedGroup);

        $this->assertFalse($resultBulkExport->yamlConfigurationValue());
        $this->assertNotSame($codeManagedGroup, $resultBulkExport);
        $this->assertNotSame($extendCodeManagedGroup, $resultBulkExport);
    }
}
