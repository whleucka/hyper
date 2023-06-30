<?php

namespace Nebula\Middleware\Request;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rate Limit Middleware
 *
 * Only allow RATE_LIMIT number of requests for
 * a sliding window of WINDOW_SIZE seconds
 */
class RateLimit extends Middleware
{
    const RATE_LIMIT = 120; // Maximum number of requests allowed per window
    const WINDOW_SIZE = 5; // Window size in seconds

    public function handle(Request $request): Middleware|Request
    {
        $requests = session()->get("requests");
        if (is_null($requests)) {
            session()->set("requests", []);
        }

        if (!$this->allowRequest()) {
            app()->tooManyRequests();
        }

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }

    /**
     * Clear the expired ts from $requests
     * @param mixed $expires
     */
    private function clearExpiredRequests($expires): void
    {
        $requests = session()->get("requests");
        foreach ($requests as $index => $timestamp) {
            if ($timestamp < $expires) {
                session()->unsetIndex("requests", $index);
            }
        }
    }

    /**
     * Determines if the request should proceed
     */
    public function allowRequest(): bool
    {
        $currentTime = time();
        $expires = $currentTime - self::WINDOW_SIZE;

        // Remove expired requests from the sliding window
        $this->clearExpiredRequests($expires);

        // Get the requests from the session, again
        $requests = session()->get("requests");

        if (count($requests) < self::RATE_LIMIT) {
            // Add the current request timestamp to the sliding window
            $requests[] = $currentTime;
            session()->set("requests", $requests);
            return true;
        }
        return false;
    }
}
