<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;

class Lifetime
{
    private $minutes = 30;

    /**
     * Limit the user session to x minutes
     * @param mixed $request
     */
    public function handle(Request $request): ?Request
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
        return $request;
    }
}
