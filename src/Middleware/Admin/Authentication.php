<?php

namespace Nebula\Middleware\Admin;

use Nebula\Interfaces\Middleware\Middleware;
use Nebula\Interfaces\Http\{Response, Request};


class Authentication implements Middleware
{
    public function handle(Request $request, \Closure $next)
    {
        // Check if the user is authenticated.
        // TODO implement me!
        // - If route has middleware auth and !isAuthenticated then 404?
        if (false) {
            // Redirect or return an error response if the user is not authenticated.
            $response = app()->get(Response::class);
            $response->setStatusCode(401);
            $response->setContent('Unauthorized.');
            return $response;
        }

        // If the user is authenticated, continue to the next Middleware or the core application logic.
        return $next($request);
    }

    private function isAuthenticated(Request $request): bool
    {
        $user = session()->get("user");
        return isset($user) && !is_null($user);
    }
}
