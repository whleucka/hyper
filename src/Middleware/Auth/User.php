<?php

namespace Nebula\Middleware\Auth;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class User
{
    /**
     * @param mixed $request
     */
    public function handle(Request $request): Request
    {
        return $request;
    }

    /**
     * @return Request
     * @param array<int,mixed> $middlware
     */
    public function authorize(Request $request): Request
    {
        // TODO look up sign-in route if auth is enabled
        $sign_in_route = "/sign-in";
        if (!isset($_SESSION["user"])) {
            $response = new RedirectResponse($sign_in_route);
            $response->send();
        }
        return $request;
    }
}
