<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Container\Container;
use Nebula\Controllers\Controller;
use StellarRouter\Route;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Whoops;
use Closure;
use Error;
use Exception;

class Web
{
    public static $instance;
    private ?Route $route;
    private Container $container;
    private ?Controller $controller;
    private Request $request;
    private Response $response;
    private Whoops\Run $whoops;
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
     * Initialize the application
     * We require the request, container, and router
     */
    public function init(): void
    {
        $this->request = $this->request();
        $this->container = $this->container();
        $this->router = $this->router();
        $this->whoops = $this->whoops();
    }

    /**
     * Instantiate the request
     */
    public function request(): Request
    {
        return Request::createFromGlobals();
    }

    /**
     * Return the app request
     */
    public function getRequest(): Request
    {
        return $this->request;
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
     * Instantiate the router
     */
    public function router(): Router
    {
        // Controller path via getControllers
        $router = $this->container->get(Router::class);
        return $router;
    }

    /**
     * Run will initialize everything and send the response
     */
    public function run(): void
    {
        $this->routing();
        $this->handle()->execute();
    }

    /**
     * Register the routes
     * Note: this method assumes attribute routing. If there are
     * routes defined before routing() is called, then it will
     * skip the attribute-based routing.
     */
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
                $this->router->registerRoutes($controllerClass);
            }
        }
    }

    /**
     * Handle the controller response
     */
    public function handle(): Web
    {
        $this->route = $this->route();
        if (!$this->route) {
            $this->pageNotFound();
        }
        $this->controller = $this->controller();
        $this->response = $this->response();
        return $this;
    }

    /**
     * Get the app route
     */
    public function getRoute(): ?Route
    {
        return $this->route;
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
    public function response(): Response
    {
        $handlerMethod = $this->route->getHandlerMethod();
        $routeParameters = $this->route->getParameters();
        //$routeMiddleware = $this->route->getMiddleware();
        $payload = $this->route->getPayload();
        $this->setupErrorHandling();
        try {
            if (!is_null($payload)) {
                $handlerResponse = $payload();
            } else {
                $handlerResponse = $this->controller->$handlerMethod(
                    ...$routeParameters
                );
            }
        } catch (Exception $ex) {
            return $this->catch($ex);
        } catch (Error $err) {
            return $this->catch($err);
        }
        return $this->isAPI()
            ? new JsonResponse([
                "ts" => time(),
                "data" => $handlerResponse,
            ])
            : new Response($handlerResponse);
    }

    public function setupErrorHandling(): void
    {
        if ($this->isAPI()) {
            $this->whoops->pushHandler(
                new Whoops\Handler\JsonResponseHandler()
            );
        } else {
            // Web response
            $this->whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
        }
    }

    public function isAPI(): bool
    {
        $middleware = $this->route?->getMiddleware();
        return !empty($middleware) && in_array("api", $middleware);
    }

    public function isDebug(): bool
    {
        $env = Env::getInstance()->env();
        return isset($env["APP_DEBUG"]) &&
            strtolower($env["APP_DEBUG"]) === "true";
    }

    public function catch(Exception|Error $problem): Response
    {
        if ($this->isDebug()) {
            $response = $this->whoops->handleException($problem);
            return $this->isAPI()
                ? new JsonResponse($response)
                : new Response($response);
        } else {
            $this->serverError();
        }
    }

    /**
     * Send the response to the client
     */
    public function execute(): void
    {
        $this->response->prepare($this->request)->send();
        //$this->logExecutionTime();
        exit();
    }

    /**
     * Send a page not found response
     */
    public function pageNotFound(): void
    {
        $content = twig("errors/404.html");
        $this->response = new Response($content, status: 404);
        $this->execute();
    }

    /**
     * Send a forbidden response
     */
    public function forbidden(): void
    {
        $content = twig("errors/403.html");
        $this->response = new Response($content, status: 403);
        $this->execute();
    }

    /**
     * Send an unauthorized response
     */
    public function unauthorized(): void
    {
        $content = twig("errors/401.html");
        $this->response = new Response($content, status: 401);
        $this->execute();
    }

    /**
     * Send a server error response
     */
    public function serverError(): void
    {
        $content = twig("errors/500.html");
        $this->response = new Response($content, status: 500);
        $this->execute();
    }

    /**
     * Log the application execution time to the error log
     */
    public function logExecutionTime(): void
    {
        $executionTime = microtime(true) - APP_START;
        $time = number_format($executionTime * 1000, 2);
        error_log("Execution time: {$time} ms");
    }

    /**
     * Init error handling using Whoops
     */
    private function whoops(): Whoops\Run
    {
        $whoops = new Whoops\Run();
        if (!$this->isDebug()) {
            return $whoops;
        }
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        return $whoops;
    }

    /**
     * Wire up a GET route
     * @param array<int,mixed> $middleware
     */
    public function get(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): Web {
        $this->router->registerRoute(
            $path,
            "GET",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a POST route
     * @param array<int,mixed> $middleware
     */
    public function post(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): Web {
        $this->router->registerRoute(
            $path,
            "POST",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a PUT route
     * @param array<int,mixed> $middleware
     */
    public function put(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): Web {
        $this->router->registerRoute(
            $path,
            "PUT",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a PATCH route
     * @param array<int,mixed> $middleware
     */
    public function patch(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): Web {
        $this->router->registerRoute(
            $path,
            "PATCH",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a DELETE route
     * @param array<int,mixed> $middleware
     */
    public function delete(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): Web {
        $this->router->registerRoute(
            $path,
            "DELETE",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }
}
