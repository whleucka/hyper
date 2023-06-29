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

        $last_activity = session()->get("LAST_ACTIVITY");
        if (
            !is_null($last_activity) &&
            time() - $last_activity > $sessionTimeout
        ) {
            session_unset();
            session_destroy();
        }
        
        session()->set("LAST_ACTIVITY", time());

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
