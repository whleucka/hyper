<?php

namespace Nebula\Middleware\Auth;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class User extends Middleware
{
    /**
     * Route authentication
     * If the route has auth defined in middleware,
     * then user must be signed in to get response
     */
    public function handle(Request $request): Middleware|Request
    {
        $sign_in_route = "/admin/sign-in";
        $middlewares = $request->attributes->route->getMiddleware();
        $session_user = session()->get("user");
        if (in_array("auth", $middlewares) && is_null($session_user)) {
            $response = new RedirectResponse($sign_in_route);
            $response->send();
            exit();
        }

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
