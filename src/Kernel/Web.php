<?php

namespace Nebula\Kernel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Dotenv\Dotenv;
use Error;
use Exception;
use GalaxyPDO\DB;
use Nebula\Controllers\Controller;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response};

class Web
{
    private Router $router;
    private ?array $route;
    private Controller $controller;
    private mixed $response;
    private DB $db;
    private $middleware;
    private Request $request;
    private array $config = [];

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
        $this->response();
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
            "db" => new \Nebula\Config\Database(),
            "path" => new \Nebula\Config\Paths(),
        ];
    }

    /**
     * Initialize PDO
     */
    private function setDB(): void
    {
        $this->db = new DB(
            $this->config["db"]->getConfig(),
            $this->config["db"]->getOptions()
        );
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
        $controllers = array_keys($this->classMap($this->config["path"]->getControllers()));
        foreach (
            $controllers
            as $controllerClass
        ) {
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
                extract($this->route);
                /**
                 *  $path
                 *  $method
                 *  $name
                 *  $middleware
                 *  $handlerClass
                 *  $handlerMethod
                 *  $parameters
                 */
                $this->controller = new $handlerClass($this->db);
                // We can maybe do something with route middleware to detect the
                // set a web request (text/html) or api request (json, etc)
                if (in_array("api", $middleware)) {
                    $this->apiResponse($handlerMethod, $parameters);
                } else {
                    $this->webResponse($handlerMethod, $parameters);
                }
            } else {
                $this->pageNotFound();
            }
        } catch (Exception $ex) {
            die("wip: payload exeception: {$ex->getMessage()}");
        } catch (Error $err) {
            die("wip: payload error: {$err->getMessage()}");
        }
    }

    /**
     * Set page not found response
     */
    public function pageNotFound(): void
    {
        // The route doesn't exist, 404
        $this->response = [
            "content" => "The page you requested doesn't seem to exist",
            "code" => Response::HTTP_NOT_FOUND,
            "headers" => ["content-type" => "text/html"],
        ];
    }

    /**
     * Set a web response
     * @param array<int,mixed> $parameters
     */
    public function webResponse(string $endpoint, array $parameters): void
    {
        $content = $this->controller->$endpoint(...$parameters);
        $this->response = [
            "content" => $content,
            "code" => Response::HTTP_OK,
            "headers" => ["content-type" => "text/html"],
        ];
    }

    /**
     * Set an API response
     * @param array<int,mixed> $parameters
     */
    public function apiResponse(string $endpoint, array $parameters): void
    {
        $content = json_encode([
            "result" => true,
            "data" => $this->controller->$endpoint(...$parameters),
            "code" => Response::HTTP_OK,
            "ts" => time(),
            "message" => "",
        ]);
        $this->response = [
            "content" => $content,
            "code" => Response::HTTP_OK,
            "headers" => [
                "content-type" => "application/json",
                "charset" => "utf-8",
            ],
        ];
    }

    /**
     * Send the response to the client
     */
    private function response(): void
    {
        $response = new Response(
            $this->response["content"],
            $this->response["code"],
            $this->response["headers"]
        );
        $response->prepare($this->request);
        $response->send();
    }

    /**
     * Terminate the request
     */
    private function terminate(): void
    {
        $this->db->close();
        exit;
    }
}
