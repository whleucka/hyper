<?php

namespace Nebula\Framework;

use Nebula\Container\Container;
use Nebula\Interfaces\Framework\Kernel;
use Nebula\Traits\Instance\Singleton;
use StellarRouter\Route;

class Application extends Container
{
    use Singleton;

    private ?Kernel $kernel = null;
    private ?string $class = null;

    /**
     * Initialize the kernel
     */
    public function init(): void
    {
        if (is_null($this->kernel)) {
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
    public function route(string $method, string $path, \Closure|string $payload = null, ?string $name = null, array $middleware = []): Application
    {
        $handlerClass = $handlerMethod = null;
        if (is_string($payload)) {
            $controllers_path = config("paths.controllers");
            $classMap = classMap($controllers_path);
            // Parse the payload
            if (!is_null($payload) && strpos($payload, '@') !== false) {
                [$handlerClass, $handlerMethod] = explode('@', $payload);
            } else if (!is_null($payload) && strpos($payload, '@') === false) {
                $handlerClass = $payload;
                $handlerMethod = 'index';
            } 
            // Verify that the controller class exists
            $found = array_filter($classMap, fn ($class) => str_contains($class, $handlerClass));
            if ($found) {
                $handlerClass = array_key_first($found);
            } else {
                throw new \Exception("Controller class not found: {$handlerClass}");
            }
        } 

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
