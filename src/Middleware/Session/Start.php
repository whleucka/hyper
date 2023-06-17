<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;

class Start
{
    /**
     * @param mixed $request
     */
    public function handle(Request $request): Request
    {
        $this->sessionCookiesHttpOnly();
        $this->enableSecureSession();
        $this->sessionStart();
        $this->limitSessionLifetime();
        return $request;
    }

    private function sessionStart(): void
    {
        session_start();
    }

    private function enableSecureSession(): void
    {
        // Enable secure session cookies
        ini_set("session.cookie_secure", true);
    }

    private function sessionCookiesHttpOnly(): void
    {
        // Set session cookie to be accessible only via HTTP
        $cookieParams = session_get_cookie_params();
        $cookieParams["httponly"] = true;
        session_set_cookie_params($cookieParams);
    }

    private function limitSessionLifetime(): void
    {
        // Set session timeout to 30 minutes
        $minutes = 30;
        $sessionTimeout = $minutes * 60; // minutes in seconds

        if (
            isset($_SESSION["LAST_ACTIVITY"]) &&
            time() - $_SESSION["LAST_ACTIVITY"] > $sessionTimeout
        ) {
            session_unset();
            session_destroy();
        }

        $_SESSION["LAST_ACTIVITY"] = time();
    }
}
