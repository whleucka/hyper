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

    /**
     * Initialize the kernel
     */
    public function init(?string $class = null): void
    {
        if (!isset($this->kernel)) {
            $this->kernel = $this->get($class ?? \Nebula\Interfaces\Http\Kernel::class);
            $this->kernel->setup($this);
        }
    }

    /**
     * Run the application
     */
    public function run(?string $class = null): void
    {
        $this->init($class);
        $response = $this->kernel->handle();
        $response->send();
        $this->kernel->terminate();
    }

    /**
     * Register a route
     */
    public function route(string $method, string $path, \Closure $payload, ?string $name = null, array $middleware = []): Application
    {
        $this->init();
        $route = new Route($path, $method, $name, $middleware, payload: $payload);
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
