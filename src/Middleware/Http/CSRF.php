<?php

namespace Nebula\Middleware\Http;

use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Interfaces\Middleware\Middleware;
use Closure;

/**
 * This middleware provides CSRF protection
 *
 * @package Nebula\Middleware\Http
 */
class CSRF implements Middleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $this->token();

    if (!$this->validate($request)) {
      $response = app()->get(Response::class);
      $response->setStatusCode(403);
      $response->setContent('Invalid CSRF token');
      return $response;
    }

    $response = $next($request);

    return $response;
  }

  public function token(): void
  {
    $token = session()->get('csrf_token');
    if (is_null($token)) {
      $token = $this->newToken();
      session()->set('csrf_token', $token);
    }
    $this->track();
  }

  public function track(): void
  {
    $token_ts = session()->get('csrf_token_ts');
    if (is_null($token_ts) || $token_ts + 3600 < time()) {
      $token = $this->newToken();
      $token_ts = time();
      session()->set('csrf_token', $token);
      session()->set('csrf_token_ts', $token_ts);
    }
  }

  public function validate(Request $request): bool
  {
    $request_method = $request->getMethod();
    if (in_array($request_method, ['GET', 'HEAD', 'OPTIONS'])) {
      return true;
    }

    $token = $request->csrf_token;
    if (!is_null($token) && hash_equals(session()->get('csrf_token'), $token)) {
      return true;
    }

    return false;
  }

  public function newToken(): string
  {
    $token = bin2hex(random_bytes(32));
    return $token;
  }
}
