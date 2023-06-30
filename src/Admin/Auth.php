<?php

namespace Nebula\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Nebula\Models\User;
use stdClass;

class Auth
{
    public static function rememberMe(User $user): bool
    {
        $duration = time() + 30 * 24 * 60 * 60;
        // Generate a random token for the user
        $token = bin2hex(random_bytes(16));

        // Store the token in a cookie that expires after $duration
        setcookie("remember_token", $token, $duration, "/");

        // Store the token in the user row
        $user->remember_me = md5($token);
        return $user->update();
    }

    public static function authenticate(stdClass $data): ?User
    {
        $user = User::findByAttribute("email", $data->email);
        return password_verify($data->password, $user?->password)
            ? $user
            : null;
    }

    public static function signOut(): void
    {
        self::destroyRememberCookie();
        session()->destroy();
        // TODO lookup route
        $route = "/admin/sign-in";
        $response = new RedirectResponse($route);
        $response->send();
        exit();
    }

    public static function destroyRememberCookie(): void
    {
        if (isset($_COOKIE["remember_token"])) {
            unset($_COOKIE["remember_token"]);
            setcookie("remember_token", "", -1, "/");
        }
    }

    public static function register(stdClass $data): ?User
    {
        $user = new User();
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = password_hash($data->password, PASSWORD_ARGON2I);
        return $user->insert();
    }

    public static function signIn(User $user): void
    {
        // TODO lookup route
        $route = "/admin";
        if (!isset($_COOKIE["remember_token"])) {
            session()->set("user", $user->id);
        }
        $response = new RedirectResponse($route);
        $response->send();
        exit();
    }
}
