<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\Framework\Kernel;
use Nebula\Traits\Instance\Singleton;
use StellarRouter\Route;

class Application extends Container
{
    use Singleton;

    private Kernel $kernel;
    private ?string $class = null;

    /**
     * Initialize the kernel
     */
    public function init(): void
    {
        if (!isset($this->kernel)) {
            $this->kernel = $this->get($this->class ?? \Nebula\Interfaces\Http\Kernel::class);
            $this->kernel->setup($this);
        }
    }

    /**
     * Run the application
     */
    public function run(?string $class = null): void
    {
        $this->class = $class;
        $this->init($class);
        $response = $this->kernel->handle();
        $response->send();
        $this->kernel->terminate();
    }

    /**
     * Register a route
     */
    public function route(string $method, string $path, \Closure $payload, ?string $handlerClass = null, ?string $handlerMethod = null, ?string $name = null, array $middleware = []): Application
    {
        $this->init();
        $route = new Route(
            path: $path, 
            method: $method, 
            name: $name, 
            middleware: $middleware, 
            handlerClass: $handlerClass, 
            handlerMethod: $handlerMethod, 
            payload: $payload
        );
        $this->kernel->router->registerRoute($route);
        return $this;
    }

    /**
     * Access the kernel space
     */
    public function use(): Kernel
    {
        return $this->kernel;
    }
}
