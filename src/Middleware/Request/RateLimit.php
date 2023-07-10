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
    private $rate_limit; // Maximum number of requests allowed per window
    private $window_size; // Window size in seconds

    public function handle(Request $request): Request
    {
        $config = config("security");
        $this->rate_limit = $config['rate_limit'];
        $this->window_size = $config['window_size'];

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
        $currentTime = time();
        $expires = $currentTime - $this->window_size;

        // Remove expired requests from the sliding window
        $this->clearExpiredRequests($expires);

        // Get the requests from the session, again
        $requests = session()->get("requests");

        if (count($requests) < $this->rate_limit) {
            // Add the current request timestamp to the sliding window
            $requests[] = $currentTime;
            session()->set("requests", $requests);
            return true;
        }
        return false;
    }
}
