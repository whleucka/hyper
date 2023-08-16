<?php

namespace Nebula\Interfaces\Routing;

use StellarRouter\Route;

interface Router
{
    public function registerClass(string $class): void;
    public function registerRoute(Route $route): void;
    public function handleRequest(
        string $requestMethod,
        string $requestUri
    ): ?Route;
    public function findRouteByName(string $name): ?Route;
    public function hasRoutes(): bool;
}
