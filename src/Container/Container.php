<?php

namespace Nebula\Container;

/**
 * Super-simple DI container
 * Job: store service bindings and handle the resolution of
 * dependencies.
 */
class Container
{
    private $bindings = [];
    private $instances = [];

    /**
     * Store the association between an interface and its
     * concrete implementation.
     * You can either use class names or closure functions
     * for the concrete implementation.
     */
    public function bind(string $interface, string|\Closure $concrete, bool $singleton = false): void
    {
        $this->bindings[$interface] = [
          'concrete' => $concrete,
          'singleton' => $singleton,
        ];
    }

    /**
     * Creates a singleton binding
     */
    public function singleton(string $interface, string|\Closure $concrete): void
    {
        $this->bind($interface, $concrete, true);
    }

    /**
     * Auto-wiring will be attempted here.
     * Singleton classes will return the same instance,
     * while non-singleton classes will create new
     * instances as usual.
     */
    public function get(string $interface)
    {
        if (isset($this->instances[$interface])) {
            return $this->instances[$interface];
        }

        if (!isset($this->bindings[$interface])) {
            // Attempt auto-wiring when no explicit binding is found.
            return $this->resolve($interface);
        }

        $binding = $this->bindings[$interface];
        $concrete = $binding['concrete'];

        if ($binding['singleton']) {
            if (!isset($this->instances[$interface])) {
                $this->instances[$interface] = $this->resolve($concrete);
            }

            return $this->instances[$interface];
        }

        return $this->resolve($concrete);
    }

    /**
     * Handle the actual instantiation and dependency
     * resolution.
     */
    private function resolve(string|\Closure $concrete)
    {
        // Check for closure instantiation
        if ($concrete instanceof \Closure) {
            $reflection = new \ReflectionFunction($concrete);
            return $concrete();
        }

        // Otherwise, try the class instantiation
        $reflection = new \ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class $concrete is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return new $concrete();
        }

        return $reflection->newInstanceArgs($this->getConstructorDependencies($constructor));
    }

    private function getConstructorDependencies(\ReflectionMethod $constructor): array
    {
        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $dependencyInterface = $parameter->getType() && !$parameter->getType()->isBuiltin()
              ? new \ReflectionClass($parameter->getType()->getName())
              : null;
            if ($dependencyInterface === null) {
                // Handle non-class dependencies (e.g., scalar values).
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                // Recursively resolve class dependencies.
                $dependencies[] = $this->get($dependencyInterface->getName());
            }
        }

        return $dependencies;
    }

    /**
     * Handle non-class dependencies, such as scalar values
     * or parameters with default values.
     * The container will either use the default value (if available)
     * or throw an exception if no default value is provided.
     */
    private function resolveNonClass(\ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \Exception("Cannot resolve non-class dependency {$parameter->name} for {$parameter->getDeclaringClass()->name}");
    }
}
