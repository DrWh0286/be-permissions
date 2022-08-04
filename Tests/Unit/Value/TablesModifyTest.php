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

use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Value\TablesModify;

/**
 * @covers \SebastianHofer\BePermissions\Value\TablesModify
 */
final class TablesModifyTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 'sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_news_domain_model_link,tx_news_domain_model_news,pages,tx_basepackage_accordion_content';

        $tablesSelect = TablesModify::createFromDBValue($dbValue);
        $expected = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_basepackage_accordion_content','tx_news_domain_model_link','tx_news_domain_model_news'];

        $this->assertSame(
            $expected,
            $tablesSelect->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content'];

        $tablesSelect = TablesModify::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'pages,sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_basepackage_accordion_content,tx_news_domain_model_link,tx_news_domain_model_news',
            (string)$tablesSelect
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromDBValue('');

        $this->assertSame([], $tablesSelect->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_db_mountpoints_object(): void //phpcs:ignore
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content'];

        $tablesSelect = TablesModify::createFromYamlConfiguration($confArray);
        $extendTablesSelect = TablesModify::createFromYamlConfiguration(['tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content']);

        $this->assertSame(
            ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_basepackage_accordion_content','tx_news_domain_model_link','tx_news_domain_model_news'],
            $tablesSelect->extend($extendTablesSelect)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_allowed_languages(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromYamlConfiguration([]);
        $this->assertSame('tables_modify', $tablesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $tablesSelect);
    }
}
