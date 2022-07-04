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

use InvalidArgumentException;
use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use SebastianHofer\BePermissions\Configuration\NoValueObjectConfiguredException;
use SebastianHofer\BePermissions\Value\ArrayBasedFieldInterface;
use SebastianHofer\BePermissions\Value\StringBasedFieldInterface;
use SebastianHofer\BePermissions\Value\TablesSelect;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Value\BeGroupFieldFactory;

/**
 * @covers \SebastianHofer\BePermissions\Value\BeGroupFieldFactory
 */
final class BeGroupFieldFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function array_based_field_can_be_created(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'tables_selected';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(TablesSelect::class);

        /** @var ArrayBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndYamlValue($fieldName, ['pages', 'tt_content']);

        $this->assertInstanceOf(ArrayBasedFieldInterface::class, $field);
        $this->assertSame('tables_select', $field->getFieldName());
        $this->assertSame(['pages', 'tt_content'], $field->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function a_string_based_field_can_be_created(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        /** @var StringBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndYamlValue($fieldName, 'group title');

        $this->assertInstanceOf(StringBasedFieldInterface::class, $field);
        $this->assertSame('title', $field->getFieldName());
        $this->assertSame('group title', $field->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function null_is_returned_if_no_class_is_configured(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'non_existing';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willThrowException(new NoValueObjectConfiguredException());

        $this->assertNull($factory->buildFromFieldNameAndYamlValue($fieldName, ''));
    }

    /**
     * @test
     */
    public function if_an_array_based_field_is_requested_the_value_must_be_an_array(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'tables_selected';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(TablesSelect::class);

        $this->expectException(InvalidArgumentException::class);
        $factory->buildFromFieldNameAndYamlValue($fieldName, '');
    }

    /**
     * @test
     */
    public function if_a_string_based_field_is_requested_the_value_must_be_a_string(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);

        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        $this->expectException(InvalidArgumentException::class);
        $factory->buildFromFieldNameAndYamlValue($fieldName, ['pages', 'tt_content']);
    }

    /**
     * @test
     */
    public function can_be_created_from_database_values(): void //phpcs:ignore
    {
        $configuration = $this->getMockBuilder(ExtensionConfigurationInterface::class)->getMock();
        $factory = new BeGroupFieldFactory($configuration);
        $fieldName = 'title';

        $configuration->expects($this->once())->method('getClassNameByFieldName')->with($fieldName)->willReturn(Title::class);

        /** @var StringBasedFieldInterface $field */
        $field = $factory->buildFromFieldNameAndDatabaseValue($fieldName, 'group title');

        $this->assertInstanceOf(StringBasedFieldInterface::class, $field);
        $this->assertSame('title', $field->getFieldName());
        $this->assertSame('group title', (string)$field);
    }
}
