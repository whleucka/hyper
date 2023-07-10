<?php

namespace Nebula\Middleware\Request;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rate Limit Middleware
 *
 * Only allow x number of requests for
 * a sliding window of y seconds
 */
class RateLimit extends Middleware
{
    public function handle(Request $request): Request
    {
        $requests = session()->get("requests");
        if (is_null($requests)) {
            session()->set("requests", []);
        }

        if (!$this->allowRequest()) {
            app()->tooManyRequests();
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
        $config = config("security");
        $rate_limit = $config["rate_limit"];
        $window_size = $config["window_size_minutes"];

        $currentTime = time();
        $expires = $currentTime - $window_size;

        // Remove expired requests from the sliding window
        $this->clearExpiredRequests($expires);

        // Get the requests from the session, again
        $requests = session()->get("requests");

        if (count($requests) < $rate_limit) {
            // Add the current request timestamp to the sliding window
            $requests[] = $currentTime;
            session()->set("requests", $requests);
            return true;
        }
        return false;
    }
}
