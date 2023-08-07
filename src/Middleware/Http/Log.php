<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

/**
 * This middleware logs requests
 *
 * @package Nebula\Middleware\Http
 */
class Log implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $route_middleware = $request->route->getMiddleware();
        if (in_array("log", $route_middleware) && env("LOG_ENABLED")) {
            $this->logRequest($request);
        }

        $response = $next($request);

        return $response;
    }

    private function logRequest(Request $request): void
    {
        // Do something with this?
        $logMessage = sprintf(
            "%s %s %s",
            $request->server("REMOTE_ADDR"),
            $request->getMethod(),
            $request->getUri(),
        );
        logger("debug", $logMessage);
    }
}
