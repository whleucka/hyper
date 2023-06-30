<?php

namespace Nebula\Middleware\Auth;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auth User Middleware
 *
 * If the route has middleware 'auth' then the user
 * must be authenticated to retrieve response
 */
class User extends Middleware
{
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
