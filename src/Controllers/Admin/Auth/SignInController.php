<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class SignInController extends Controller
{
    #[Get("/admin/sign-in", "auth.sign_in")]
    public function sign_in(): string
    {
        return twig("admin/auth/sign-in.html");
    }

    #[Post("/admin/sign-in", "auth.sign_in_post")]
    public function sign_in_post(): string
    {
        if (
            !$this->validate([
                "email" => ["required", "string", "email"],
                "password" => ["required", "string"],
            ])
        ) {
            return $this->sign_in();
        }

        if ($user = Auth::authenticate()) {
            $remember_me = request()->get("remember_me");
            if ($remember_me) {
                Auth::rememberMe($user);
            }
            if (Auth::twoFactorEnabled()) {
                session()->set("two_fa_user", $user->uuid);
                app()->redirect("auth.sign_in_2fa");
            } else {
                Auth::signIn($user);
            }
        } else {
            // Add a custom validation error for bad password
            Validate::addError("password", "Bad email or password");
            // We can trigger an error with no message. In this case,
            // the email field will turn red with no message.
            Validate::addError("email");
            return $this->sign_in();
        }
    }
}
