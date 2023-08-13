<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

/**
 * This middleware returns a JSON response
 * if the response content is an array
 *
 * @package Nebula\Middleware\Http
 */
class JsonResponse implements Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    $content = $response->getContent();

    if (is_array($content)) {
      $response->setHeader('Content-Type', 'application/json');
      $response->setContent(json_encode($content));
    }

    return $response;
  }
}
