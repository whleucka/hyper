<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;

class Cookies
{
    /**
     * Enable secure session cookies
     * Session cookie to be accessible only via HTTP
     * @param mixed $request
     */
    public function handle(Request $request): Request
    {
        ini_set("session.cookie_secure", true);
        $cookieParams = session_get_cookie_params();
        $cookieParams["httponly"] = true;
        session_set_cookie_params($cookieParams);
        return $request;
    }
}
