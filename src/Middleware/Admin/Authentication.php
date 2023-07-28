<?php

namespace Nebula\Middleware\Admin;

use Nebula\Interfaces\Middleware\Middleware;
use Nebula\Interfaces\Http\{Response, Request};
use Nebula\Traits\Http\Response as HttpResponse;
use Closure;

class Authentication implements Middleware
{
    use HttpResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $middleware = $request->route?->getMiddleware();
        if (is_array($middleware) && in_array('auth', $middleware)) {
            // Redirect or return an error response if the user is not authenticated.
            return $this->response(401, "Unauthorized");
        }

        $response = $next($request);
        
        return $response;
    }

    private function isAuthenticated(Request $request): bool
    {
        $user = session()->get("user");
        return isset($user) && !is_null($user);
    }
}
