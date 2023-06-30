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
    private $sign_in_route = "/admin/sign-in";
    public function handle(Request $request): Middleware|Request
    {
        // Get route middleware
        $middlewares = $request->attributes->route->getMiddleware();

        // Only affects auth routes
        if (in_array("auth", $middlewares)) {
            $user_id = session()->get("user");
            $token = $_COOKIE["remember_token"] ?? null;
            if ($token) {
                $this->cookieAuth($token);
            } else {
                $this->sessionAuth($user_id);
            }
        }

        if ($this->next !== null) {
            return $this->next->handle($request);
        }

        return $request;
    }

    /**
     * Validate cookie and set app user
     */
    private function cookieAuth(string $token): void
    {
        $user = User::findByAttribute("remember_me", md5($token));
        if ($user) {
            // User exists
            $user = new User($user->id);
            app()->setUser($user);
        } else {
            // No users found with this token
            Auth::destroyRememberCookie();
            $this->redirectSignIn();
        }
    }

    /**
     * Validate session and set app user
     */
    private function sessionAuth(?string $user_id): void
    {
        $user = User::find($user_id);
        if ($user) {
            $user = new User($user_id);
            app()->setUser($user);
        } else {
            $this->redirectSignIn();
        }
    }

    private function redirectSignIn(): void
    {
        $response = new RedirectResponse($this->sign_in_route);
        $response->send();
        exit();
    }
}
