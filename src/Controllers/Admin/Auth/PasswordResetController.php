<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
use StellarRouter\{Get, Post};

class PasswordResetController extends Controller
{
    #[Get("/admin/password-reset/{uuid}/{token}", "auth.password_reset")]
    public function password_reset(string $uuid, string $token): string
    {
        $user = User::findByAttribute("uuid", $uuid);
        if (!Auth::validateForgotPassword($user, $token)) {
            app()->forbidden();
        }
        return twig("admin/auth/password-reset.html");
    }

    #[Post("/admin/password-reset/{uuid}/{token}", "auth.password_reset_post")]
    public function password_reset_post(string $uuid, string $token): string
    {
        $user = User::findByAttribute("uuid", $uuid);
        if (!Auth::validateForgotPassword($user, $token)) {
            app()->forbidden();
        }
        $request = $this->validate([
            "password_check" => [
                "Password" => ["required", "match"],
            ],
            ...RegisterController::$password_rules,
        ]);
        if ($request) {
            if (Auth::changePassword($user, $request->password)) {
                Auth::signIn($user);
            }
        }
        return $this->password_reset($uuid, $token);
    }
}