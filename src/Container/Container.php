<?php

namespace Nebula\Container;

use ReflectionClass;
use ReflectionFunction;
use ReflectionParameter;
use Exception;
use Closure;

/**
 * Super-simple DI container
 * Job: store service bindings and handle the resolution of
 * dependencies.
 * Issues: circular dependencies eating memory up
 */
class Container
{
    private $bindings = [];
    private $instances = [];
    private $currentlyResolving = [];

    /**
     * Bind and interface to a concrete class or closure instantiated class
     * Singleton classes should return the same instance.
     */
    public function bind(string $interface, string|Closure $concrete, bool $singleton = false): void
    {
        $this->bindings[$interface] = [
            'concrete' => $concrete,
            'singleton' => $singleton,
            'resolved' => false,
        ];
    }

    /**
     * Shorthand singleton
     */
    public function singleton(string $interface, string|Closure $concrete): void
    {
        $this->bind($interface, $concrete, true);
    }

    /**
     * Return instance of interface
     */
    public function get(string $interface): mixed
    {
        // Check if the class is being resolved to prevent circular dependencies.
        if (isset($this->currentlyResolving[$interface])) {
            // TODO not sure if this is working, circular dep problems still exist
            throw new Exception("Circular dependency detected for interface $interface.");
        }

        if (isset($this->instances[$interface])) {
            return $this->instances[$interface];
        }

        if (!isset($this->bindings[$interface])) {
            // If the interface is not bound, attempt auto-wiring.
            return $this->resolve($interface);
        }

        $binding = $this->bindings[$interface];
        $concrete = $binding['concrete'];

        $this->currentlyResolving[$interface] = true;

        if ($binding['singleton'] && !$binding['resolved']) {
            $this->instances[$interface] = $this->resolve($concrete);
            $this->bindings[$interface]['resolved'] = true;
        }

        unset($this->currentlyResolving[$interface]);

        return $this->instances[$interface] ?? $this->resolve($concrete);
    }

    /**
     * Resolve a concrete class w/ dependencies
     */
    private function resolve(string|Closure $concrete): mixed
    {
        // Check for closure instantiation
        if ($concrete instanceof Closure) {
            $reflection = new ReflectionFunction($concrete);
            return $concrete();
        }

        $reflection = new ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw new Exception("Class $concrete is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $dependencyInterface = $parameter->getClass();

            if ($dependencyInterface === null) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                // Recursively resolve class dependencies.
                $dependencies[] = $this->get($dependencyInterface->getName());
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Resolve a non-class dependencies (e.g. scalar values, etc)
     * Use a default if available (e.g. public $bar = 1; is equal to int(1) )
     */
    private function resolveNonClass(ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception("Cannot resolve non-class dependency {$parameter->name} for {$parameter->getDeclaringClass()->name}");
    }
}
