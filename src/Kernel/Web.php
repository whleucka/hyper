<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Container\Container;
use Nebula\Controllers\Controller;
use StellarRouter\Route;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Closure;

class Web
{
    public static $instance;
    private ?Route $route;
    private Container $container;
    private ?Controller $controller;
    private Request $request;
    private Response $response;
    public Router $router;

    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
            static::$instance->init();
        }

        return static::$instance;
    }

    /**
     * Run will initialize everything and prepare the response
     */
    public function run(): void
    {
        $this->routing();
        $this->handle()->execute();
    }

    public function init(): void
    {
        $this->request = $this->request();
        $this->container = $this->container();
        $this->router = $this->router();
    }

    public function handle(): self
    {
        $this->route = $this->route();
        if (!$this->route) {
            $this->pageNotFound();
        }
        $this->controller = $this->controller();
        $this->response = $this->response();
        return $this;
    }

    public function pageNotFound(): void
    {
        $content = twig("errors/404.html");
        $this->response = new Response($content, status: 404);
        $this->execute();
    }

    /**
     * Instantiate the request
     */
    public function request(): Request
    {
        return Request::createFromGlobals();
    }

    /**
     * Instantiate the router
     */
    public function router(): Router
    {
        // Controller path via getControllers
        $router = $this->container->get(Router::class);
        return $router;
    }

    public function routing(): void
    {
        if ($this->router->hasRoutes()) {
            return;
        }
        $config = new \Nebula\Config\Paths();
        $controllers = array_keys(
            ClassMapGenerator::createMap($config->getControllers())
        );
        if ($controllers) {
            foreach ($controllers as $controllerClass) {
                $controller = $this->container->get($controllerClass);
                $this->router->registerRoutes($controller::class);
            }
        }
    }

    /**
     * Instantiate the DI container
     */
    public function container(): Container
    {
        $container = Container::getInstance();
        $config = new \Nebula\Config\Container();
        $container->setDefinitions($config->getDefinitions())->build();
        return $container;
    }

    /**
     * Instantiate the route
     */
    public function route(): ?Route
    {
        return $this->router->handleRequest(
            $this->request->getMethod(),
            "/" . $this->request->getPathInfo()
        );
    }

    /**
     * Instantiate the controller
     */
    public function controller(): ?Controller
    {
        $handlerClass = $this->route->getHandlerClass();
        return $handlerClass ? $this->container->get($handlerClass) : null;
    }

    /**
     * Instantiate the response
     */
    public function response(): JsonResponse|Response
    {
        $handlerMethod = $this->route->getHandlerMethod();
        $routeParameters = $this->route->getParameters();
        $routeMiddleware = $this->route->getMiddleware();
        $payload = $this->route->getPayload();
        if (!is_null($payload)) {
            $handlerResponse = $payload;
        } else {
            $handlerResponse = $this->controller->$handlerMethod(
                ...$routeParameters
            );
        }
        return in_array("api", $routeMiddleware)
            ? new JsonResponse(["data" => $handlerResponse])
            : new Response($handlerResponse);
    }

    /**
     * Send the response to the client
     */
    public function execute(): void
    {
        $this->response->prepare($this->request)->send();
        exit();
    }

    /**
     * Wire up a GET route
     */
    public function get($path, $handlerClass = "", $handlerMethod = "", string $name = "", array $middleware = [], ?Closure $payload = null): Web
    {
        $this->router->registerRoute(
            $path,
            "GET",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            !is_null($payload) && is_callable($payload) ? $payload() : null,
        );
        return $this;
    }

    /**
     * Wire up a POST route
     */
    public function post($path, $handlerClass = "", $handlerMethod = "", string $name = "", array $middleware = [], ?Closure $payload = null): Web
    {
        $this->router->registerRoute(
            $path,
            "POST",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            !is_null($payload) && is_callable($payload) ? $payload() : null,
        );
        return $this;
    }

    /**
     * Wire up a PUT route
     */
    public function put($path, $handlerClass = "", $handlerMethod = "", string $name = "", array $middleware = [], ?Closure $payload = null): Web
    {
        $this->router->registerRoute(
            $path,
            "PUT",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            !is_null($payload) && is_callable($payload) ? $payload() : null,
        );
        return $this;
    }

    /**
     * Wire up a PATCH route
     */
    public function patch($path, $handlerClass = "", $handlerMethod = "", string $name = "", array $middleware = [], ?Closure $payload = null): Web
    {
        $this->router->registerRoute(
            $path,
            "PATCH",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            !is_null($payload) && is_callable($payload) ? $payload() : null,
        );
        return $this;
    }

    /**
     * Wire up a DELETE route
     */
    public function delete($path, $handlerClass = "", $handlerMethod = "", string $name = "", array $middleware = [], ?Closure $payload = null): Web
    {
        $this->router->registerRoute(
            $path,
            "DELETE",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            !is_null($payload) && is_callable($payload) ? $payload() : null,
        );
        return $this;
    }
}
