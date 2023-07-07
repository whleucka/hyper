<?php

namespace Nebula\Middleware\Route;

use Nebula\Admin\Auth;
use Nebula\Middleware\Middleware;
use Nebula\Models\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authorize Middleware
 *
 * If the route has middleware 'auth' then the user
 * must be authenticated to retrieve response
 */
class Authorize extends Middleware
{
    private $sign_in_route;
    private $admin_route;

    public function handle(Request $request): Request
    {
        $middlewares = $request->attributes->route->getMiddleware();

        $user_id = session()->get("user");
        $token = $_COOKIE["remember_token"] ?? null;
        $user = $token
            ? $this->cookieAuth($token)
            : $this->sessionAuth($user_id);

        if ($user) {
            app()->setUser($user);
        }

        // Only affects auth routes
        if (in_array("auth", $middlewares) && !$user) {
            $this->redirectSignIn();
        }

        return $request;
    }

    /**
     * Validate cookie and set app user
     */
    private function cookieAuth(string $token): ?User
    {
        $user = User::findByAttribute("remember_token", md5($token));
        if ($user) {
            return new User($user->getId());
        }
        return null;
    }

    /**
     * Validate session and set app user
     */
    private function sessionAuth(?string $user_id): ?User
    {
        $user = User::find($user_id);
        if ($user) {
            return new User($user_id);
        }
        return null;
    }

    private function redirectSignIn(): void
    {
        Auth::destroyRememberCookie();
        app()->redirect("auth.sign_in");
    }
}
