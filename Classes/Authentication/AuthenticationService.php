<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Authentication;

use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthenticationService implements AuthenticationServiceInterface
{
    private ExtensionConfigurationInterface $extensionConfiguration;

    public function __construct(ExtensionConfigurationInterface $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * @throws AuthenticationFailedException
     */
    public function authenticate(ServerRequestInterface $request): void
    {
        $sentApiToken = $request->getHeader('apiToken');
        $expectedApiToken = $this->extensionConfiguration->getApiToken();

        if (implode('', $sentApiToken) == $expectedApiToken && strlen($expectedApiToken) >= 10) {
            return;
        }

        throw new AuthenticationFailedException('The api token verification failed!');
    }
}
