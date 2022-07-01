<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationServiceInterface
{
    /**
     * @throws AuthenticationFailedException
     */
    public function authenticate(ServerRequestInterface $request): void;
}
