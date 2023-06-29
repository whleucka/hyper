<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

class Lifetime extends Middleware
{
    private $minutes = 30;

    /**
     * Limit the user session to x minutes
     */
    public function handle(Request $request): Middleware|Request
    {
        $sessionTimeout = $this->minutes * 60; // minutes in seconds

        if (
            isset($_SESSION["LAST_ACTIVITY"]) &&
            time() - $_SESSION["LAST_ACTIVITY"] > $sessionTimeout
        ) {
            session_unset();
            session_destroy();
        }

        $_SESSION["LAST_ACTIVITY"] = time();

        // If authentication succeeds, call the next middleware
        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
