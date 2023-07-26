<?php

namespace Nebula\Traits;

use Closure;

trait RegisterRoute
{
    /**
     * Wire up a GET route
     * @param array<int,mixed> $middleware
     */
    public function get(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): self {
        app()->getRouter()->registerRoute(
            $path,
            "GET",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a POST route
     * @param array<int,mixed> $middleware
     */
    public function post(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): self {
        app()->getRouter()->registerRoute(
            $path,
            "POST",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a PUT route
     * @param array<int,mixed> $middleware
     */
    public function put(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): self {
        app()->getRouter()->registerRoute(
            $path,
            "PUT",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a PATCH route
     * @param array<int,mixed> $middleware
     */
    public function patch(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): self {
        app()->getRouter()->registerRoute(
            $path,
            "PATCH",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }

    /**
     * Wire up a DELETE route
     * @param array<int,mixed> $middleware
     */
    public function delete(
        string $path,
        string $handlerClass = "",
        string $handlerMethod = "",
        string $name = "",
        array $middleware = [],
        ?Closure $payload = null
    ): self {
        app()->getRouter()->registerRoute(
            $path,
            "DELETE",
            $name,
            $middleware,
            $handlerClass,
            $handlerMethod,
            is_callable($payload) ? $payload : null
        );
        return $this;
    }
}
