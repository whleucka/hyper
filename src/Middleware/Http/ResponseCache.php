<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

class ResponseCache implements Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    if (env("REDIS_ENABLED")) {
      $config = config('redis');
      $client = new \Predis\Client($config);

      $cacheKey = 'cache:' . $request->getUri(); // Generate a unique cache key based on the request URI

      // Attempt to retrieve the cached response from Redis
      $cachedResponse = $client->get($cacheKey);

      if (!is_null($cachedResponse)) {
        // If cached response exists, return it immediately
        $content = unserialize($cachedResponse);
      }

      // If the response is not cached, proceed to the next middleware to generate the response
      $response = $next($request);

      // Cache the response if it's cacheable (e.g., successful responses with cache-control headers)
      if ($response->getStatusCode() === 200 && $response->hasHeader('Cache-Control')) {
        $cacheDuration = 1200; // 20 min
        $serializedResponse = serialize($response);

        // Store the response in Redis with the specified cache duration
        $client->setex($cacheKey, $cacheDuration, $serializedResponse);
      }
    } else {
      $response = $next($request);
    }

    return $response;
  }
}
