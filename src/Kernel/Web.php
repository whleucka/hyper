<?php

namespace Nebula\Kernel;

use GalaxyPDO\DB;
use Dotenv\Dotenv;
use StellarRouter\Router;
use Symfony\Component\HttpFoundation\Request;
use Composer\ClassMapGenerator\ClassMapGenerator;

class Web
{
    private Router $router;
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
        $this->routing();
        $this->handleRequest();
        $this->payload();
        $this->handleExceptions();
        $this->response();
        $this->terminate();
    }

    /**
     * Set up essential components such as environment, configurations, db, etc
     */
    private function bootstrap(): void
    {
        $this->env();
        $this->config();
        $this->db();
    }

    /**
     * Load .env secrets
     */
    private function env(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        // .env is required in the web root
        $dotenv->load();
    }

    /**
     * Load application configurations
     */
    private function config(): void
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
    private function db(): void
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
            "auth_user" => \Nebula\Middleware\Authentication\User::class,
        ];
    }

    /**
     * Route to the correct controller endpoint
     */
    private function routing(): void
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

    public function classMap(string $path): array
    {
        return ClassMapGenerator::createMap($path);
    }

    /**
     * Handle in the incoming requests and send through middleware stack
     */
    private function handleRequest(): void
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
    }

    /**
     * Execute the controller method (controller interacts with models, prepares response)
     */
    private function payload(): void
    {
    }

    /**
     * Handle any errors / exceptions, logging, etc
     */
    private function handleExceptions(): void
    {
    }

    /**
     * Send the response to the client
     */
    private function response(): void
    {
    }

    /**
     * Terminate the request
     */
    private function terminate(): void
    {
    }
}
