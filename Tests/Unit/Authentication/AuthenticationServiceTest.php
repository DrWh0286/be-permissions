<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Authentication;

use Pluswerk\BePermissions\Authentication\AuthenticationFailedException;
use Pluswerk\BePermissions\Authentication\AuthenticationService;
use Pluswerk\BePermissions\Configuration\ExtensionConfigurationInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Authentication\AuthenticationService
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
