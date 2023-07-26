<?php

namespace App\Http;

use Nebula\Framework\Application;
use Nebula\Interfaces\Http\{Request, Response};
use Nebula\Interfaces\Routing\Router;
use Nebula\Interfaces\System\Kernel as NebulaKernel;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Throwable;

class Kernel implements NebulaKernel
{
    private Router $router;
    private Application $app;

    /**
     * Setup the application
     */
    public function setup(Application $app): Kernel
    {
        $this->app = $app;
        $this->registerInterfaces();
        $this->initRouter();
        return $this;
    }

    /**
     * Register all default the framework interface binding
     */
    public function registerInterfaces(): void
    {
        // $this->app->singleton(\Nebula\Interfaces\Database\Database::class, \Nebula\Database\MySQLDatabase::class);
        $this->app->bind(\Nebula\Interfaces\Routing\Router::class, \Nebula\Routing\Router::class);
        $this->app->bind(\Nebula\Interfaces\Http\Response::class, \Nebula\Http\Response::class);
    }

    public function initRouter(): void
    {
        $this->router = $this->app->get(Router::class);
        // Register the controller classes
        $controller_dir = __DIR__ . "/../Controllers";
        foreach ($this->classMap($controller_dir) as $class_name => $filename) {
            $this->router->registerClass($class_name);
        }
    }

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
        $response = $this->app->get(Response::class);
        $route = $this->router->handleRequest($request->getMethod(), $request->getUri());

        if ($route) {

            try {
                $handlerClass = $route->getHandlerClass();
                $handlerMethod = $route->getHandlerMethod();
                $routeParameters = $route->getParameters();
                $class = new $handlerClass;
                
                $content = $class->$handlerMethod(...$routeParameters);
                $response->setStatusCode(200);
                $response->setContent($content ?? '');

            } catch (\Exception $ex) {
                $this->handleException($ex);
            }

        }

        return $response;
    }

    /**
     * Handle any application exceptions
     */
    public function handleException(Throwable $exception): void
    {
        // TODO deal with the exception
        throw $exception;
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    {
        exit;
    }
}
