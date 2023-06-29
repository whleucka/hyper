<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

class Cookies extends Middleware
{
    /**
     * Enable secure session cookies
     * Session cookie to be accessible only via HTTP
     */
    public function handle(Request $request): Middleware|Request
    {
        ini_set("session.cookie_secure", true);
        $cookieParams = session_get_cookie_params();
        $cookieParams["httponly"] = true;
        session_set_cookie_params($cookieParams);

        // If authentication succeeds, call the next middleware
        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
