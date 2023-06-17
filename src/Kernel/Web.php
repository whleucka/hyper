<?php

namespace Nebula\Kernel;

use GalaxyPDO\DB;
use Dotenv\Dotenv;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\{Request, Response};
use Composer\ClassMapGenerator\ClassMapGenerator;
use Exception;

class Web
{
    private Router $router;
    private ?array $route;
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
            $this->config["db"]->config,
            $this->config["db"]->options
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
        foreach (
            $this->classMap($this->config["path"]->controllers)
            as $controllerClass => $path
        ) {
            $controller = new $controllerClass();
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
        $this->route = $this->router->handleRequest($this->request->getMethod(), '/' . $this->request->getPathInfo());
    }

    /**
     * Execute the controller method (controller interacts with models, prepares response)
     */
    private function executePayload(): void
    {
        try {
            if ($this->route) {
                extract($this->route);
                $controller = new $class;
                // We can maybe do something with route middleware to detect the 
                // set a web request (text/html) or api request (json, etc)
                $this->response = [
                    'content' => $controller->$endpoint(...$parameters),
                    'code' => Response::HTTP_OK,
                    'headers' => ['content-type' => 'text/html'],
                ];
            } else {
                // The route doesn't exist, 404
                $this->response = [
                    'content' => "The page you requested doesn't seem to exist",
                    'code' => Response::HTTP_NOT_FOUND,
                    'headers' => ['content-type' => 'text/html'],
                ];
            }
        } catch (Exception $ex) {
            die("wip: payload exeception: {$ex->getMessage()}");
        }
    }

    /**
     * Send the response to the client
     */
    private function response(): void
    {
        $response = new Response(
            $this->response['content'],
            $this->response['code'],
            $this->response['headers'],
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
    }
}
