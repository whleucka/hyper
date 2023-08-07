<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

class CachedResponse implements Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    if (env("REDIS_ENABLED")) {

      $config = config('redis');
      $client = new \Predis\Client($config);
      $cacheKey = 'cache:' . $request->getUri(); // Generate a unique cache key based on the request URI

      // Cache the response if it's cacheable (e.g., successful responses with cache-control headers)
      if ($response->getStatusCode() === 200 && $response->hasHeader('Cache-Control')) {
        $cacheDuration = 604800; // Default cache duration is 7 days
        $serializedResponse = serialize($response);

        // Store the response in Redis with the specified cache duration
        $client->setex($cacheKey, $cacheDuration, $serializedResponse);
      }
    }

    return $response;
  }
}
