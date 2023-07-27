<?php

namespace Nebula\Routing;

use Nebula\Interfaces\Routing\Router as NebulaRouter;
use StellarRouter\Router as StellarRouter;
use StellarRouter\Route;

class Router implements NebulaRouter
{
    private $router;

    public function __construct()
    {
      $this->router = new StellarRouter();
    }
    public function registerClass(string $class): void
    {
      $this->router->registerClass($class);
    }

    public function registerRoute(Route $route): void
    {
      $this->router->registerRoute($route);
    }

    public function handleRequest(string $requestMethod, string $requestUri): ?Route
    {
      return $this->router->handleRequest($requestMethod, $requestUri);
    }
}
