<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

/**
 * Cookies middleware
 *
 * Secure cookies
 * Session cookie to be accessible only via HTTP
 */
class Cookies extends Middleware
{
    public function handle(Request $request): Request
    {
        if (!app()->isDebug()) {
            $this->secureCookies();
            $this->cookiesHTTPOnly();
        }

        return $request;
    }

    private function secureCookies(): void
    {
        ini_set("session.cookie_secure", true);
    }

    private function cookiesHTTPOnly(): void
    {
        $cookieParams = session_get_cookie_params();
        $cookieParams["httponly"] = true;
        session_set_cookie_params($cookieParams);
    }
}
