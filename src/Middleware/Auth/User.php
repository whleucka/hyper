<?php

namespace Nebula\Middleware\Auth;

use Nebula\Middleware\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class User extends Middleware
{
    public function handle(Request $request): Middleware|Request
    {
        $sign_in_route = "/admin/sign-in";
        $middlewares = $request->attributes->route->getMiddleware();
        if (in_array('auth', $middlewares) && !isset($_SESSION["user"])) {
            $response = new RedirectResponse($sign_in_route);
            $response->send();
            exit();
        }

        // If authentication succeeds, call the next middleware
        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }
}
