<?php

namespace Nebula\Traits;

use Error;
use StellarRouter\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait RouterMethods
{
    /**
     * Find a route by route name
     */
    public function findRoute(string $name): ?Route
    {
        $routes = app()->getRouter()->getRoutes();
        $exists = array_filter($routes, fn ($route) => $route["name"] === $name);
        if (!empty($exists) && count($exists) === 1) {
            $route = reset($exists);
            return new Route(...$route);
        }
        return null;
    }

    /**
     * @param mixed $args
     */
    public function moduleRoute(string $name, ...$args): ?string
    {
        $name_arr = explode(".", $name);
        $first = $name_arr[0];
        $end = end($name_arr);
        $route = $this->findRoute("module.$end");
        if (!is_null($route)) {
            return $this->buildRoute($route->getName(), $first, ...$args);
        }
        return null;
    }

    public function routePathURL(string $route_path): string
    {
        $url = config("app")["url"];
        return $url . $route_path;
    }

    /**
     * Build a route with params
     * @param mixed $params
     */
    public function buildRoute(string $name, ...$params): ?string
    {
        $route = $this->findRoute($name);
        if (!is_null($route)) {
            $regex = "#({[\w\?]+})#";
            $uri = $route->getPath();
            preg_match_all($regex, $uri, $matches);
            if ($matches) {
                array_walk(
                    $matches[0],
                    fn (&$item) => ($item =
                        "#" . str_replace("?", "\?", $item) . "#")
                );
                return preg_replace($matches[0], $params, $uri);
            }
        }
        return null;
    }

    public function redirect(string $route_name): void
    {
        $route = $this->findRoute($route_name);
        if (!$route) {
            throw new Error("Route cannot be found: $route_name");
        }
        $response = new RedirectResponse($route->getPath());
        $response->send();
        exit();
    }

    public function redirectUrl(string $url): void
    {
        $response = new RedirectResponse($url);
        $response->send();
        exit();
    }
}
