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
    const MINUTES = 30;

    public function handle(Request $request): Middleware|Request
    {
        $sessionTimeout = self::MINUTES * 60; // minutes in seconds

        $last_activity = session()->get("last_activity");
        if (
            !is_null($last_activity) &&
            time() - $last_activity > $sessionTimeout
        ) {
            session()->destroy();
        }

        session()->set("last_activity", time());

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
