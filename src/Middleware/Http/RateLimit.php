<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;
use Nebula\Traits\Http\Response as NebulaResponse;

/**
 * This middleware provides rate limiting
 *
 * @package Nebula\Middleware\Http
 */
class RateLimit implements Middleware
{
  use NebulaResponse;

  public function handle(Request $request, Closure $next): Response
  {
    $route_middleware = $request->route?->getMiddleware();
    if (env("REDIS_ENABLED") && $route_middleware && in_array("rate_limit", $route_middleware)) {
      $result = $this->rateLimit();
      if (!$result) {
        return $this->response(429, "Too many requests");
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

    $rateLimit = $config['requests_per'] ?? 25; // Number of requests allowed per minute
    $ipKey = "ip:$ipAddress";

    // Add the current timestamp to the Redis Sorted Set
    $timestamp = time();
    $client->zadd($ipKey, $timestamp, $timestamp);

    // Remove any timestamps that exceed the rate limit window
    $windowStart = $timestamp - $config['rate_limit_seconds'] ?? 60;
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
