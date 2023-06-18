<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Dotenv\Dotenv;
use Error;
use Exception;
use GalaxyPDO\DB;
use Nebula\Controllers\Controller;
use StellarRouter\{Route, Router};
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class Web
{
    private array $middleware;
    private ?DB $db = null;
    private ?Route $route = null;
    private Controller $controller;
    private Request $request;
    private Router $router;
    private array $config = [];
    private mixed $response;

    /**
     * The application lifecycle
     */
    public function run(): void
    {
        $this->bootstrap();
        $this->loadMiddleware();
        $this->registerRoutes();
        $this->request();
        $this->executePayload();
        $this->terminate();
    }

    /**
     * Set up essential components such as environment, configurations, db, etc
     */
    private function bootstrap(): void
    {
        $this->loadEnv();
        $this->setConfig();
        $this->setDB();
    }

    /**
     * Load .env secrets
     */
    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        // .env is required in the web root
        $dotenv->load();
    }

    /**
     * Load application configurations
     */
    private function setConfig(): void
    {
        // Database configuration
        $this->config = [
            "debug" => strtolower($_ENV["APP_DEBUG"]) === "true",
            "db" => new \Nebula\Config\Database(),
            "path" => new \Nebula\Config\Paths(),
        ];
    }

    /**
     * Initialize PDO
     */
    private function setDB(): void
    {
        $config = $this->config["db"]->getConfig();
        if (strtolower($config["enabled"]) === "true") {
            $this->db = new DB(
                $this->config["db"]->getConfig(),
                $this->config["db"]->getOptions()
            );
        }
    }

    /**
     * Load the middleware to process incoming requests
     */
    private function loadMiddleware(): void
    {
        $this->middleware = [
            "session_start" => \Nebula\Middleware\Session\Start::class,
            "auth_user" => \Nebula\Middleware\Auth\User::class,
        ];
    }

    /**
     * Route to the correct controller endpoint
     */
    private function registerRoutes(): void
    {
        $this->router = new Router();
        $controllers = array_keys(
            $this->classMap($this->config["path"]->getControllers())
        );
        foreach ($controllers as $controllerClass) {
            $controller = new $controllerClass($this->db);
            $this->router->registerRoutes($controller::class);
        }
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
    private function request(): void
    {
        $request = Request::createFromGlobals();
        foreach ($this->middleware as $alias => $middleware) {
            $class = new $middleware();
            // We may define other match-arms to provide
            // additional arguments to handle here
            $request = match ($alias) {
                default => $class->handle($request),
            };
        }
        $this->request = $request;
        $this->route = $this->router->handleRequest(
            $this->request->getMethod(),
            "/" . $this->request->getPathInfo()
        );
    }

    /**
     * Execute the controller method (controller interacts with models, prepares response)
     */
    private function executePayload(): void
    {
        try {
            if ($this->route) {
                $handlerMethod = $this->route->getHandlerMethod();
                $handlerClass = $this->route->getHandlerClass();
                $middleware = $this->route->getMiddleware();
                $parameters = $this->route->getParameters();
                $this->controller = new $handlerClass($this->db);
                if (in_array("api", $middleware)) {
                    $this->apiResponse($handlerMethod, $parameters);
                } else {
                    $this->webResponse($handlerMethod, $parameters);
                }
            } else {
                $this->pageNotFound();
            }
        } catch (Exception $ex) {
            if ($this->config["debug"]) {
                if (in_array("api", $middleware)) {
                    $this->apiException($ex);
                }
            }
            $this->terminate();
        } catch (Error $err) {
            if ($this->config["debug"]) {
                if (in_array("api", $middleware)) {
                    $this->apiError($err);
                }
            }
            $this->terminate();
        }
    }

    /**
     * Set api exception response
     */
    public function apiException(Exception $exception): void
    {
        // TODO detect .env mode and if dev, show message otherwise ??
        $content = [
            "success" => false,
            "message" => $exception->getMessage(),
            "code" => $exception->getCode(),
            "ts" => time(),
        ];
        $this->response = new JsonResponse($content);
        $this->response->prepare($this->request);
        $this->response->send();
    }

    /**
     * Set api exception response
     */
    public function apiError(Error $error): void
    {
        // TODO detect .env mode and if dev, show message otherwise ??
        $content = [
            "success" => false,
            "message" => $error->getMessage(),
            "code" => $error->getCode(),
            "ts" => time(),
        ];
        $this->response = new JsonResponse($content);
        $this->response->prepare($this->request);
        $this->response->send();
    }

    /**
     * Set page not found response
     */
    public function pageNotFound(): void
    {
        // The route doesn't exist, 404
        $this->response = new Response(status: 404);
        $this->response->prepare($this->request);
        $this->response->send();
        $this->terminate();
    }

    /**
     * Set a web response
     * @param array<int,mixed> $parameters
     */
    public function webResponse(string $endpoint, array $parameters): void
    {
        $content = $this->controller->$endpoint(...$parameters);
        $this->response = new Response($content);
        $this->response->prepare($this->request);
        $this->response->send();
    }

    /**
     * Set an API response
     * @param array<int,mixed> $parameters
     */
    public function apiResponse(string $endpoint, array $parameters): void
    {
        $content = [
            "success" => true,
            "data" => $this->controller->$endpoint(...$parameters),
            "ts" => time(),
        ];
        $this->response = new JsonResponse($content);
        $this->response->prepare($this->request);
        $this->response->send();
    }

    /**
     * Terminate the request
     */
    private function terminate(): void
    {
        $this->db?->close();
        exit();
    }
}
