<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

/**
 * This middleware pushes url history via HX-Push-Url
 *
 * @package Nebula\Middleware\Http
 */
class PushUrl implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $route_middleware = $request->route?->getMiddleware();
        if (
            $route_middleware &&
            preg_grep("/push-url/", $route_middleware)
        ) {
            $response->setHeader("HX-Push-Url", $request->route->getPath());
        }

        return $response;
    }
}
