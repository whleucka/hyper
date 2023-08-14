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
    $config = config('redis');
    if ($config['enabled'] && $route_middleware && (preg_grep("/rate_limit/", $route_middleware) || in_array("api", $route_middleware))) {
      $index = middlewareIndex($route_middleware, 'rate_limit');
      $route_setting = str_replace('rate_limit=', '', $route_middleware[$index]);
      $default = $config['requests_per_second'];
      $rps = $route_setting && $route_setting !== 'rate_limit' ? intval($route_setting) : $default;
      $result = $this->rateLimit($rps);
      if (!$result) {
        return $this->response(429, "Too many requests");
      }
    }
    $response = $next($request);

    return $response;
  }

  private function rateLimit(int $rps): bool
  {
    $config = config('redis');
    $client = new \Predis\Client($config);

    $ipAddress = ip();

    $rateLimit = intval($rps); // Number of requests allowed per window
    $ipKey = "ip:$ipAddress";

    // Add the current timestamp to the Redis Sorted Set
    $timestamp = time();
    $client->zadd($ipKey, $timestamp, $timestamp);

    // Remove any timestamps that exceed the rate limit window
    $windowStart = $timestamp - $config['rps_window_seconds'] ?? 60;
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
