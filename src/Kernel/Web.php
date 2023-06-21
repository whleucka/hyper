<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Dotenv\Dotenv;
use Error;
use Exception;
use Nebula\Container\Container;
use Nebula\Controllers\Controller;
use StellarRouter\{Route, Router};
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Whoops;

class Web
{
    private $whoops;
    private ?Request $request = null;
    private ?Route $route = null;
    private Container $container;
    private Controller $controller;
    private Response $response;
    private Router $router;
    private array $config = [];
    private array $middleware = [];

    /**
     * The application lifecycle
     */
    public function run(): void
    {
        $this->bootstrap()
            ?->loadMiddleware()
            ?->registerRoutes()
            ?->request()
            ?->executePayload()
            ?->terminate();
    }

    /**
     * Set up essential components such as environment, configurations, DI container, etc
     */
    private function bootstrap(): ?self
    {
        return $this->loadEnv()
            ?->setConfig()
            ?->setContainer()
            ?->errorHandler();
    }

    /**
     * Load .env secrets
     */
    private function loadEnv(): ?self
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        // .env is required in the web root
        $dotenv->load();
        return $this;
    }

    /**
     * Load application configurations
     */
    private function setConfig(): ?self
    {
        $this->config = [
            "debug" => strtolower($_ENV["APP_DEBUG"]) === "true",
            "container" => new \Nebula\Config\Container(),
            "paths" => new \Nebula\Config\Paths(),
        ];
        return $this;
    }

    /**
     * Load error handling
     */
    private function errorHandler(): ?self
    {
        $whoops = new Whoops\Run();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $this->whoops = $whoops;
        return $this;
    }

    /**
     * Setup DI container
     */
    private function setContainer(): ?self
    {
        $this->container = Container::getInstance()
            ->setDefinitions($this->config["container"]->getDefinitions())
            ->build();
        return $this;
    }

    /**
     * Load the middleware to process incoming requests
     * Note: handle method will be called for all middleware
     */
    private function loadMiddleware(): ?self
    {
        $this->middleware = [
            "system" => [
                "session.cookies" => \Nebula\Middleware\Session\Cookies::class,
                "session.lifetime" =>
                    \Nebula\Middleware\Session\Lifetime::class,
                "session.start" => \Nebula\Middleware\Session\Start::class,
                "session.csrf" => \Nebula\Middleware\Session\CSRF::class,
            ],
            "route" => [
                "auth" => \Nebula\Middleware\Auth\User::class,
            ],
        ];
        return $this;
    }

    /**
     * Route to the correct controller endpoint
     */
    private function registerRoutes(): ?self
    {
        $this->router = $this->container->get(Router::class);
        $controllers = array_keys(
            $this->classMap($this->config["paths"]->getControllers())
        );
        foreach ($controllers as $controllerClass) {
            $controller = new $controllerClass($this->request);
            $this->router->registerRoutes($controller::class);
        }
        return $this;
    }

    /**
     * @return array<class-string,non-empty-string>
     */
    public function classMap(string $path): array
    {
        return ClassMapGenerator::createMap($path);
    }

    /**
     * Handle in the incoming requests and send through middleware stack
     */
    private function request(): ?self
    {
        $request = Request::createFromGlobals();
        // Run the system middleware
        $request = $this->systemMiddleware($request);
        // Set the route
        $this->route = $this->router->handleRequest(
            $request->getMethod(),
            "/" . $request->getPathInfo()
        );
        // Bind the middleware to the request
        $request->attributes->middleware = $this->route?->getMiddleware();
        // Run the route middleware
        if ($this->route) {
            $request = $this->routeMiddleware($request);
        }
        $this->request = $request;
        return $this;
    }

    /**
     * System-specific middleware
     */
    private function systemMiddleware(?Request $request): ?Request
    {
        if (!$request) {
            return $request;
        }
        $system_middlewares = $this->middleware["system"];
        if ($system_middlewares) {
            foreach ($system_middlewares as $alias => $middleware) {
                $class = $this->container->get($middleware);
                // Always call handle, but if there are other methods to call,
                // you can call them here
                $request = match ($alias) {
                    default => $class->handle($request),
                };
            }
        }
        return $request;
    }

    /**
     * Route-specific middleware
     */
    private function routeMiddleware(?Request $request): ?Request
    {
        if (!$request) {
            return $request;
        }
        $route_middlewares = $request->attributes->middleware;
        if ($route_middlewares) {
            foreach ($route_middlewares as $route_middleware) {
                if (isset($this->middleware["route"][$route_middleware])) {
                    $middleware = $this->middleware["route"][$route_middleware];
                    $class = $this->container->get($middleware);
                    // Same thing here, route middleware calls handle, but you
                    // can call any method here
                    $request = match ($route_middleware) {
                        default => $class->handle($request),
                    };
                }
            }
        }
        return $request;
    }

    /**
     * Execute the controller method (controller interacts with models, prepares response)
     */
    private function executePayload(): ?self
    {
        try {
            if ($this->route) {
                $this->controllerResponse();
            } else {
                $this->pageNotFound();
            }
        } catch (Exception $ex) {
            $this->handleException($ex);
        } catch (Error $err) {
            $this->handleError($err);
        }
        return $this;
    }

    /**
     * Handle controller exception
     */
    private function handleException(Exception $exception): void
    {
        $middleware = $this->route->getMiddleware();
        if (in_array("api", $middleware)) {
            $this->apiException($exception);
        } else {
            $this->webException($exception);
        }
    }

    /**
     * Handle controller error
     */
    private function handleError(Error $error): void
    {
        $middleware = $this->route->getMiddleware();
        if (in_array("api", $middleware)) {
            $this->apiError($error);
        } else {
            $this->webException($error);
        }
    }

    /**
     * The response from the controller method
     */
    private function controllerResponse(): void
    {
        $handlerMethod = $this->route->getHandlerMethod();
        $handlerClass = $this->route->getHandlerClass();
        $middleware = $this->route->getMiddleware();
        $parameters = $this->route->getParameters();
        // Instantiate the controller
        $this->controller = new $handlerClass($this->request);
        // Now we decide what to do
        $controller_response = $this->controller->$handlerMethod(
            ...$parameters
        );
        if (in_array("api", $middleware)) {
            $this->whoops->pushHandler(
                new Whoops\Handler\JsonResponseHandler()
            );
            $this->apiResponse($controller_response);
        } else {
            $this->whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
            $this->webResponse($controller_response);
        }
    }

    /**
     * Set web exception response
     */
    public function webException(Exception|Error $exception): void
    {
        if (!$this->config["debug"]) {
            return;
        }
        $html = $this->whoops->handleException($exception);
        $this->webResponse($html);
    }

    /**
     * Set api exception response
     */
    public function apiException(Exception $exception): void
    {
        if (!$this->config["debug"]) {
            return;
        }
        $error = $this->whoops->handleException($exception);
        $error = json_decode($error);
        $this->apiResponse($error->error, "EXCEPTION", false);
    }

    /**
     * Set api exception response
     */
    public function apiError(Error $error): void
    {
        if (!$this->config["debug"]) {
            return;
        }
        $this->apiResponse($error->getMessage(), "ERROR", false);
    }

    /**
     * Set page not found response
     */
    public function pageNotFound(): void
    {
        $this->webResponse(code: 404);
    }

    /**
     * Set a web response
     * The response could be a twig template or something else
     * @param mixed $content
     */
    public function webResponse(mixed $content = "", int $code = 200): void
    {
        $this->response = new Response($content, $code);
        if ($this->request) {
            $this->response->prepare($this->request);
        }
        $this->response->send();
    }

    /**
     * Set an API response
     * Always returns a JSON response
     * @param mixed $status
     * @param mixed $success
     */
    public function apiResponse(
        mixed $data = [],
        $status = "OK",
        $success = true
    ): void {
        $content = [
            "status" => $status,
            "success" => $success,
            "data" => $data,
            "ts" => time(),
        ];
        $this->response = new JsonResponse($content);
        if ($this->request) {
            $this->response->prepare($this->request);
        }
        $this->response->send();
    }

    /**
     * Terminate the request
     */
    private function terminate(): void
    {
        if ($this->config["debug"]) {
            $stop = (microtime(true) - APP_START) * 1000;
            error_log(
                sprintf("Execution time: %s ms", number_format($stop, 2))
            );
        }
        exit();
    }
}
