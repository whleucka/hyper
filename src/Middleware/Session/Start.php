<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;

class Start
{
    /**
     * Start the application session
     * @param mixed $request
     */
    public function handle(Request $request): ?Request
    {
        session_start();
        return $request;
    }
}
