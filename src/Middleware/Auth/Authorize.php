<?php

namespace Nebula\Middleware\Auth;

use Nebula\Admin\Auth;
use Nebula\Middleware\Middleware;
use Nebula\Models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authorize Middleware
 *
 * If the route has middleware 'auth' then the user
 * must be authenticated to retrieve response
 */
class Authorize extends Middleware
{
    public function handle(Request $request): Middleware|Request
    {
        $sign_in_route = "/admin/sign-in";
        $middlewares = $request->attributes->route->getMiddleware();
        $session_user = session()->get("user");
        $token = $_COOKIE['remember_token'] ?? null;
        if ($token) {
            $user = User::findByAttribute('remember_me', md5($token));
            if (!$user) {
                // No users found with this token
                $token = null;
                Auth::destroyRememberCookie();
            } else {
                // Set the user session if not already done
                $user = session()->get('user');
                if (!$user) {
                    session()->set("user", $user->id);
                }
            }
        }
        
        if (in_array("auth", $middlewares) && (is_null($token) && is_null($session_user))) {
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
