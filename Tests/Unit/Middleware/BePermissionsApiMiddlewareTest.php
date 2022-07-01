<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Middleware;

use Pluswerk\BePermissions\Authentication\AuthenticationFailedException;
use Pluswerk\BePermissions\Authentication\AuthenticationServiceInterface;
use Pluswerk\BePermissions\Middleware\BePermissionsApiMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Middleware\BePermissionsApiMiddleware
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
