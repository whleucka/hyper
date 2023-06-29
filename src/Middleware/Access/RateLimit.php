<?php

namespace Nebula\Middleware\Access;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;

class RateLimit extends Middleware
{
    // Maybe keep it on the lower window size
    // to keep the requests array small
    const RATE_LIMIT = 100; // Maximum number of requests allowed per window
    const WINDOW_SIZE = 1; // Window size in seconds

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

    private function clearExpiredRequests($expires): void
    {
        $requests = session()->get("requests");
        foreach ($requests as $index => $timestamp) {
            if ($timestamp < $expires) {
                session()->unsetIndex("requests", $index);
            }
        }
    }

    public function allowRequest(): bool
    {
        $currentTime = time();
        $expires = $currentTime - self::WINDOW_SIZE;

        // Remove expired requests from the sliding window
        $this->clearExpiredRequests($expires);

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
