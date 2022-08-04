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
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;

/**
 * @covers \SebastianHofer\BePermissions\Value\ExplicitAllowDeny
 */
final class ExplicitAllowDenyTest extends UnitTestCase
{
    /**
     * @test
     */
    public function no_error_with_empty_database_field(): void //phpcs:ignore
    {
        ExplicitAllowDeny::createFromDBValue('');
    }

    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 'tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:CType:header:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW';

        $explicitAllowDeny = ExplicitAllowDeny::createFromDBValue($dbValue);

        $this->assertSame(
            [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'another_pluginb' => 'ALLOW',
                        'some_plugina' => 'ALLOW'
                    ]
                ]
            ],
            $explicitAllowDeny->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $configArray = [
            'tt_content' => [
                'CType' => [
                    'text' => 'ALLOW',
                    'header' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'ALLOW'
                ]
            ]
        ];

        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration($configArray);

        $this->assertSame(
            'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:another_pluginb:ALLOW,tt_content:list_type:some_plugina:ALLOW',
            (string)$explicitAllowDeny
        );
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_explicit_allow_deny_object(): void //phpcs:ignore
    {
        $baseConfigArray = [
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'ALLOW'
                ]
            ]
        ];

        $baseExplicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration($baseConfigArray);

        $extendingConfigArray = [
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'additionia_field' => 'ALLOW'
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'DENY',
                    'z_plugin' => 'ALLOW'
                ]
            ]
        ];

        $extendingExplicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration($extendingConfigArray);

        $expectedConfigArray = [
            'tt_content' => [
                'CType' => [
                    'additionia_field' => 'ALLOW',
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ],
                'list_type' => [
                    'another_pluginb' => 'DENY',
                    'some_plugina' => 'ALLOW',
                    'z_plugin' => 'ALLOW'
                ]
            ]
        ];

        $expectedExplicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration($expectedConfigArray);

        $this->assertEquals($expectedExplicitAllowDeny, $baseExplicitAllowDeny->extend($extendingExplicitAllowDeny));
    }

    /**
     * @test
     */
    public function field_name_is_explicit_allowdeny(): void //phpcs:ignore
    {
        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration([]);
        $this->assertSame('explicit_allowdeny', $explicitAllowDeny->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $explicitAllowDeny);
    }
}
