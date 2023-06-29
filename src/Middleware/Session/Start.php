<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

class Start extends Middleware
{
    /**
     * Start the application session
     */
    public function handle(Request $request): Middleware|Request
    {
        @session_start();

        // If authentication succeeds, call the next middleware
        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
