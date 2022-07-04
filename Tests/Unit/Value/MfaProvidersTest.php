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
use SebastianHofer\BePermissions\Value\MfaProviders;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\MfaProviders
 */
final class MfaProvidersTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 'totp';

        $mfaProviders = MfaProviders::createFromDBValue($dbValue);

        $this->assertSame(
            ['totp'],
            $mfaProviders->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = ['totp'];

        $mfaProviders = MfaProviders::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'totp',
            (string)$mfaProviders
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_mfa_providers_array_is_returned(): void //phpcs:ignore
    {
        $mfaProviders = MfaProviders::createFromDBValue('');

        $this->assertSame([], $mfaProviders->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_mfa_providers_object(): void //phpcs:ignore
    {
        $confArray = ['totp'];

        $mfaProviders = MfaProviders::createFromYamlConfiguration($confArray);
        $extendMfaProviders = MfaProviders::createFromYamlConfiguration(['recovery-codes']);

        $this->assertSame(
            ['recovery-codes', 'totp'],
            $mfaProviders->extend($extendMfaProviders)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_mfa_providers(): void //phpcs:ignore
    {
        $mfaProviders = MfaProviders::createFromYamlConfiguration([]);
        $this->assertSame('mfa_providers', $mfaProviders->getFieldName());
    }

    /**
     * @test
     */
    public function extends_AbstractStringArrayField(): void //phpcs:ignore
    {
        $mfaProviders = MfaProviders::createFromYamlConfiguration([]);
        $this->assertInstanceOf(AbstractStringArrayField::class, $mfaProviders);
    }
}
