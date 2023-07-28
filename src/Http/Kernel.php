<?php

namespace Nebula\Http;

use Dotenv\Dotenv;
use Nebula\Interfaces\Http\{Request, Response};
use Nebula\Interfaces\Database\Database;
use Nebula\Interfaces\Routing\Router;
use Nebula\Interfaces\Http\Kernel as NebulaKernel;
use Composer\ClassMapGenerator\ClassMapGenerator;
use App\Config\Config;
use Nebula\Middleware\Middleware;
use Nebula\Traits\Http\Response as HttpResponse;
use StellarRouter\Route;
use Throwable;

class Kernel implements NebulaKernel
{
    use HttpResponse;

    private Router $router;
    private Database $db;
    private Dotenv $dotenv;
    protected array $middleware;

    /**
     * Setup the application
     */
    public function setup(): Kernel
    {
        $this->registerMiddleware();
        $this->registerRoutes();
        return $this;
    }

    /**
     * Initialize the router and register classes
     */
    private function registerRoutes(): void
    {
        $this->router = app()->get(Router::class);
        // Register the controller classes
        $config = app()->get(Config::class)::paths();
        foreach ($this->classMap($config['controllers']) as $class_name => $filename) {
            $this->router->registerClass($class_name);
        }
    }

    private function registerMiddleware(): void
    {
        foreach ($this->middleware as $i => $class) {
            $this->middleware[$i] = app()->get($class);
        }
    }

    /**
     * Return the app environment variables
     */
    public function getEnvironment(string $name): ?string
    {
        if (!isset($this->dotenv)) {
            // Load environment variables
            $config = app()->get(Config::class)::paths();
            $this->dotenv = Dotenv::createImmutable($config['app_root']);
            $this->dotenv->safeLoad();
        }
        return isset($_ENV[$name])
            ? $_ENV[$name]
            : null;
    }

    /**
     * Return the app database
     */
    public function getDatabase(): Database
    {
        if (!isset($this->db)) {
            $this->db = app()->get(Database::class);
            $config = app()->get(Config::class)::database();
            $this->db->connect($config);
        }
        return $this->db;
    }

    /**
     * @return array<class-string,non-empty-string>
     */
    public function classMap(string $directory): array
    {
        if (!file_exists($directory)) {
            throw new \Exception("class map directory doesn't exist");
        }
        return ClassMapGenerator::createMap($directory);
    }

    /**
     * Resolve the route and execute controller method
     */
    public function resolveRoute(?Route $route, Request $request): Response
    {
        $response = app()->get(Response::class);
        if ($route) {
            try {
                $handlerClass = $route->getHandlerClass();
                $handlerMethod = $route->getHandlerMethod();
                $routeParameters = $route->getParameters();
                $class = new $handlerClass($request);

                $content = $class->$handlerMethod(...$routeParameters);
                $response->setContent($content ?? '');
            } catch (\Exception $ex) {
                return $this->handleException($ex);
            }
        } else {
            return $this->response(404, "Page not found");
        }
        return $response;
    }

    /**
     * Handle the request and return a response
     */
    public function handleRequest(Request $request): Response
    {
        // Figure out the route
        $route = $this->router->handleRequest($request->getMethod(), $request->getUri());
        // Save the route to the request (e.g. use in middleware)
        $request->route = $route;
        $runner = app()->get(Middleware::class);
        $response = $runner
            ->layer($this->middleware)
            ->handle($request, fn() => $this->resolveRoute($route, $request));
        return $response;
    }

    /**
     * Handle any application exceptions
     */
    public function handleException(Throwable $exception): Response
    {
        return $this->response(500, "Server error");
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    {
        exit;
    }
}
