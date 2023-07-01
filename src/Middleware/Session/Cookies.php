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
    public function handle(Request $request): Middleware|Request
    {
        $this->secureCookies();
        $this->cookiesHTTPOnly();

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }

    private function secureCookies(): void
    {
        if (!app()->isDebug()) {
            ini_set("session.cookie_secure", true);
        }
    }

    private function cookiesHTTPOnly(): void
    {
        $cookieParams = session_get_cookie_params();
        $cookieParams["httponly"] = true;
        session_set_cookie_params($cookieParams);
    }
}
