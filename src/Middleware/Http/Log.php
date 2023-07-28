<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\Request;
use Nebula\Interfaces\Middleware\Middleware;

class Log implements Middleware
{
    public function handle(Request $request, \Closure $next)
    {
    die("here");
        // Log the incoming request data.
        $this->logRequest($request);

        return $next($request);
    }

    private function logRequest(Request $request): void
    {
        $logMessage = sprintf(
            "[%s] %s %s",
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getUri(),
        );
        dump($logMessage);
    }
}
