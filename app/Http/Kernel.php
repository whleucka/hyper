<?php

namespace App\Http;

use Nebula\Interfaces\Database\Database;
use Dotenv\Dotenv;
use Nebula\Interfaces\Http\{Request, Response};
use Nebula\Interfaces\Routing\Router;
use Nebula\Interfaces\System\Kernel as NebulaKernel;
use Composer\ClassMapGenerator\ClassMapGenerator;
use App\Config\Config;
use Throwable;

final class Kernel implements NebulaKernel
{
    private Router $router;
    private Database $db;
    private Dotenv $dotenv;

    /**
     * Setup the application
     */
    public function setup(): Kernel
    {
        $this->registerInterfaces();
        $this->initRouter();
        return $this;
    }

    /**
     * Register all default the framework interface binding
     */
    public function registerInterfaces(): void
    {
        app()->singleton(\Nebula\Interfaces\Database\Database::class, \Nebula\Database\MySQLDatabase::class);
        app()->bind(\Nebula\Interfaces\Routing\Router::class, \Nebula\Routing\Router::class);
        app()->bind(\Nebula\Interfaces\Http\Response::class, \Nebula\Http\Response::class);
    }

    public function initRouter(): void
    {
        $this->router = app()->get(Router::class);
        // Register the controller classes
        $controller_dir = __DIR__ . "/../Controllers";
        foreach ($this->classMap($controller_dir) as $class_name => $filename) {
            $this->router->registerClass($class_name);
        }
    }

    public function getEnvironment(string $name): ?string
    {
        if (!isset($this->dotenv)) {
            // Load environment variables
            $env_path = __DIR__ . "/../../";
            $this->dotenv = Dotenv::createImmutable($env_path);
            $this->dotenv->safeLoad();
        }
        return isset($_ENV[$name])
            ? $_ENV[$name]
            : null;
    }

    public function getDatabase(): Database
    {
        if (!isset($this->db)) {
            $this->db = app()->get(Database::class);
            $config = app()->get(Config::class)::database();
            dd($config);
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
     * Handle the request and return a response
     */
    public function handleRequest(Request $request): Response
    {
        $response = app()->get(Response::class);
        $route = $this->router->handleRequest($request->getMethod(), $request->getUri());

        if ($route) {
            try {
                $handlerClass = $route->getHandlerClass();
                $handlerMethod = $route->getHandlerMethod();
                $routeParameters = $route->getParameters();
                $class = new $handlerClass();

                $content = $class->$handlerMethod(...$routeParameters);

                // Set the Expires header to cache the resource for one hour (3600 seconds).
                $expires = 3600;
                $response->setHeader("Expires", gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

                $response->setContent($content ?? '');

            } catch (\Exception $ex) {
                $this->handleException($ex);
            }
        } else {
            // TODO 404 page
            $response->setStatusCode(404);
            $response->setContent('Page not found.');
        }

        return $response;
    }

    /**
     * Handle any application exceptions
     */
    public function handleException(Throwable $exception): void
    {
        // TODO deal with the exception
        $response = app()->get(Response::class);
        $response->setStatusCode(404);
        $response->setContent('Server error.');
        $response->send();
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    {
        exit;
    }
}
