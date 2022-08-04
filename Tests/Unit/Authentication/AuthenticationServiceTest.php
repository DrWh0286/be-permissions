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

namespace SebastianHofer\BePermissions\Tests\Unit\Authentication;

use SebastianHofer\BePermissions\Authentication\AuthenticationFailedException;
use SebastianHofer\BePermissions\Authentication\AuthenticationService;
use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Authentication\AuthenticationService
 */
final class AuthenticationServiceTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider mismatchingApiTokenProvider
     * @param string[] $sentApiToken
     * @param string $configuredApiToken
     * @throws AuthenticationFailedException
     */
    public function authentication_fails_in_case_of_mismatching_api_token(array $sentApiToken, string $configuredApiToken): void //phpcs:ignore
    {
        $configuration = $this->createMock(ExtensionConfigurationInterface::class);

        $authenticationService = new AuthenticationService($configuration);

        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects($this->once())->method('getHeader')->with('apiToken')->willReturn($sentApiToken);
        $configuration->expects($this->once())->method('getApiToken')->willReturn($configuredApiToken);

        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage('The api token verification failed!');

        $authenticationService->authenticate($request);
    }

    /**
     * @return array<string, array<string,array<int, string>|string>>
     */
    public function mismatchingApiTokenProvider(): array
    {
        return [
            'simple mismatch' => [
                'sentApiToken' => ['972ht908whf029f509h98hjfhidsfzfzctdopkeß0904367tf'],
                'configuredApiToken' => 'coniguredapitroken123'
            ],
            'token is matching, but to short (< 10 characters)' => [
                'sentapiToken' => ['veryshort'],
                'configuredApiToken' => 'veryshort'
            ]
        ];
    }
}
