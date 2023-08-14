<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

/**
 * This middleware caches responses in Redis
 * Route must have "cached" middleware defined
 *
 * @package Nebula\Middleware\Http
 */
class CachedResponse implements Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $route_middleware = $request->route?->getMiddleware();
    $config = config('redis');
    $enabled = $config['enabled'];
    if ($enabled && $route_middleware && preg_grep("/cache/", $route_middleware)) {
      // Create a new Redis client
      $client = new \Predis\Client($config);
      // Get the cache duration from the route middleware
      $index = middlewareIndex($route_middleware, 'cache');
      $ttl = str_replace('cache=', '', $route_middleware[$index]);
      // Convert the cache duration to an integer
      $cacheDuration = $ttl !== 'cache' ? intval($ttl) : $config['cache_default_ttl'];

      // Generate a unique cache key based on the request URI
      $cacheKey = 'cache:' . $request->getUri(); // Generate a unique cache key based on the request URI

      // Attempt to retrieve the cached response from Redis
      $cachedResponse = $client->get($cacheKey);

      if (!is_null($cachedResponse)) {
        // If cached response exists, return it immediately
        $cachedResponse = unserialize($cachedResponse);
        $cachedResponse->setHeader('Cache-Control', 'max-age=' . $cacheDuration . ', public');
        return $cachedResponse;
      }

      // If the response is not cached, proceed to the next middleware to generate the response
      $response = $next($request);

      // Cache the response
      if ($response->getStatusCode() === 200) {
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
