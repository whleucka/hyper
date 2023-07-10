<?php

namespace Nebula\Middleware\Session;

use Symfony\Component\HttpFoundation\Request;
use Nebula\Middleware\Middleware;

/**
 * Session lifetime middleware
 *
 * Destroy the session if last_activty is older than $minutes
 */
class Lifetime extends Middleware
{
    public function handle(Request $request): Request
    {
        $sessionTimeout = config("security")["session_lifetime"]; // In seconds

        $last_activity = session()->get("last_activity");
        if (
            !is_null($last_activity) &&
            time() - $last_activity > $sessionTimeout
        ) {
            session()->destroy();
        }

        session()->set("last_activity", time());

        return $request;
    }
}
