<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Container\Container;
use Nebula\Controllers\Controller;
use StellarRouter\Route;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};

class Web
{
    public static $instance;
    private ?Route $route;
    private Container $container;
    private Controller $controller;
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
        $this->response = new Response(status: 404);
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
    public function controller(): Controller
    {
        $handlerClass = $this->route->getHandlerClass();
        return $this->container->get($handlerClass);
    }

    /**
     * Instantiate the response
     */
    public function response(): JsonResponse|Response
    {
        $handlerMethod = $this->route->getHandlerMethod();
        $routeParameters = $this->route->getParameters();
        $routeMiddleware = $this->route->getMiddleware();
        // Now we decide what to do
        $handlerResponse = $this->controller->$handlerMethod(
            ...$routeParameters
        );
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

    public function get($path, $handlerClass, $handlerMethod, string $name = "", array $middleware = []): Web {
        $this->router->registerRoute(
            $path,
            "GET",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod
        );
        return $this;
    }

    public function post($path, $handlerClass, $handlerMethod, string $name = "", array $middleware = []): Web {
        $this->router->registerRoute(
            $path,
            "POST",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod
        );
        return $this;
    }

    public function put($path, $handlerClass, $handlerMethod, string $name = "", array $middleware = []): Web {
        $this->router->registerRoute(
            $path,
            "PUT",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod
        );
        return $this;
    }

    public function patch($path, $handlerClass, $handlerMethod, string $name = "", array $middleware = []): Web {
        $this->router->registerRoute(
            $path,
            "PATCH",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod
        );
        return $this;
    }

    public function delete($path, $handlerClass, $handlerMethod, string $name = "", array $middleware = []): Web {
        $this->router->registerRoute(
            $path,
            "DELETE",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod
        );
        return $this;
    }
}
