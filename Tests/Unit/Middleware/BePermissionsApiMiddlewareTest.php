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

namespace SebastianHofer\BePermissions\Tests\Unit\Middleware;

use SebastianHofer\BePermissions\Authentication\AuthenticationFailedException;
use SebastianHofer\BePermissions\Authentication\AuthenticationServiceInterface;
use SebastianHofer\BePermissions\Middleware\BePermissionsApiMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Middleware\BePermissionsApiMiddleware
 */
final class BePermissionsApiMiddlewareTest extends UnitTestCase
{
    /**
     * @test
     */
    public function if_api_endpoint_does_not_match_simply_the_request_is_handled(): void //phpcs:ignore
    {
        $container = $this->createMock(ContainerInterface::class);
        $cache = $this->createMock(FrontendInterface::class);
        $authenticationService = $this->createMock(AuthenticationServiceInterface::class);
        $middleware = new BePermissionsApiMiddleware($container, $cache, $authenticationService);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $dummyResponse = $this->createMock(ResponseInterface::class);

        $request->expects($this->once())->method('getRequestTarget')->willReturn('/some/target');
        $handler->expects($this->once())->method('handle')->with($request)->willReturn($dummyResponse);

        $response = $middleware->process($request, $handler);

        $this->assertSame($dummyResponse, $response);
    }

    /**
     * @test
     */
    public function unauthorized_response_is_returned_if_authentication_fails(): void //phpcs:ignore
    {
        $container = $this->createMock(ContainerInterface::class);
        $cache = $this->createMock(FrontendInterface::class);
        $authenticationService = $this->createMock(AuthenticationServiceInterface::class);
        $middleware = new BePermissionsApiMiddleware($container, $cache, $authenticationService);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects($this->once())->method('getRequestTarget')->willReturn('/be-permissions-api/some/route');

        $authenticationService
            ->expects($this->once())
            ->method('authenticate')
            ->willThrowException(new AuthenticationFailedException('The api token verification failed!'));

        $handler->expects($this->never())->method('handle');

        $response = $middleware->process($request, $handler);

        $this->assertSame(401, $response->getStatusCode());
    }
}
