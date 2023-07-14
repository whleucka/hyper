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
        if (in_array("auth", $middlewares) && is_null($user)) {
            $this->redirectSignIn();
        }

        return $request;
    }

    /**
     * Validate cookie and set app user
     */
    private function cookieAuth(string $token): ?User
    {
        return User::findByAttribute("remember_token", md5($token));
    }

    /**
     * Validate session and set app user
     */
    private function sessionAuth(?string $user_id): ?User
    {
        return User::find($user_id);
    }

    private function redirectSignIn(): void
    {
        Auth::destroyRememberCookie();
        app()->redirect("auth.sign_in");
    }
}
