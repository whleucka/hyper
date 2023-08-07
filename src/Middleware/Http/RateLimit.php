<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

class RateLimit implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (env("REDIS_ENABLED")) {
            $result = $this->rateLimit();
            if (!$result) {
                $response = app()->get(Response::class);
                $response->setStatusCode(429);
                $response->setContent('Rate limit exceeded');
                return $response;
            }
        }
        $response = $next($request);

        return $response;
    }

    private function rateLimit(): bool
    {
        $config = config('redis');
        $client = new \Predis\Client($config);

        $ipAddress = requestIp();

        $rateLimit = 25; // Number of requests allowed per minute
        $ipKey = "ip:$ipAddress";

        // Add the current timestamp to the Redis Sorted Set
        $timestamp = time();
        $client->zadd($ipKey, $timestamp, $timestamp);

        // Remove any timestamps that exceed the rate limit window
        $windowStart = $timestamp - 60; // 60 seconds = 1 minute window
        $client->zremrangebyscore($ipKey, 0, $windowStart);

        // Get the number of requests made from the IP address in the window
        $requestsInWindow = $client->zcard($ipKey);

        if ($requestsInWindow > $rateLimit) {
            // IP address has exceeded the rate limit, return an error response
            return false;
        }

        return true;
    }
}
