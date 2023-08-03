<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

class Log implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $this->logRequest($request);

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
