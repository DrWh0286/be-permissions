<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Middleware;

use Doctrine\Common\Annotations\AnnotationReader;
use Pluswerk\BePermissions\Authentication\AuthenticationFailedException;
use Pluswerk\BePermissions\Authentication\AuthenticationServiceInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;

final class BePermissionsApiMiddleware implements MiddlewareInterface
{
    private FrontendInterface $cache;
    private ContainerInterface $container;
    private AuthenticationServiceInterface $authenticationService;

    public function __construct(ContainerInterface $container, FrontendInterface $cache, AuthenticationServiceInterface $authenticationService)
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->authenticationService = $authenticationService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $entryPoint = '/be-permissions-api';

        if (str_starts_with($request->getRequestTarget(), '/' . trim($entryPoint, '/') . '/')) {
            // @todo: Make authentication extendable. And a bit more flexible.
            try {
                $this->authenticationService->authenticate($request);
            } catch (AuthenticationFailedException $exception) {
                return new JsonResponse(['success' => false, 'error' => 'Authentication failure!'], 401);
            }

            $cacheIdentifier = 'person_api_routes';
            $routes = null;

            if ($this->cache->get($cacheIdentifier) === false) {
                $loader = new AnnotationDirectoryLoader(
                    new FileLocator(Environment::getExtensionsPath() . '/be_permissions/Classes/Api/'),
                    new AnnotatedRouteControllerLoader(new AnnotationReader())
                );

                $routes = $loader->load(Environment::getExtensionsPath() . '/be_permissions/Classes/Api/');

                $writeValue = serialize($routes);
                $this->cache->set($cacheIdentifier, $writeValue);
            } else {
                $cachedValue = $this->cache->get($cacheIdentifier);

                if (is_string($cachedValue)) {
                    $routes = unserialize($cachedValue);
                }
            }

            // @todo: Think of better handling here (Exception/Error Response)!
            if (!($routes instanceof RouteCollection)) {
                return new JsonResponse(['success' => false, 'error' => 'No routes found!'], 500);
            }

            $symfonyRequest = Request::create((string)$request->getUri());
            $context = (new RequestContext())->fromRequest($symfonyRequest);

            $urlMatcher = new UrlMatcher($routes, $context);

            $controllerResolver = new ControllerResolver($this->container);
            $argumentsResolver = new ArgumentResolver();

            $symfonyRequest->attributes->add($urlMatcher->match($symfonyRequest->getPathInfo()));
            $controller = $controllerResolver->getController($symfonyRequest);

            $response = new JsonResponse(['success' => false, 'error' => 'No controller action found for url ' . $request->getUri()], 500);

            // @todo: Think of better handling here (Exception/Error Response)!
            if ($controller !== false) {
                $arguments = $argumentsResolver->getArguments($symfonyRequest, $controller);
                /** @var JsonResponse $response */
                $response = call_user_func_array($controller, $arguments);
            }

            return $response;
        }

        return $handler->handle($request);
    }
}