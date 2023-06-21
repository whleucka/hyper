<?php

namespace Nebula\Middleware\Auth;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class User
{
    /**
     * @param mixed $request
     */
    public function handle(Request $request): ?Request
    {
        $sign_in_route = "/sign-in";
        if (!isset($_SESSION["user"])) {
            $response = new RedirectResponse($sign_in_route);
            $response->send();
            exit();
        }
        return $request;
    }
}
