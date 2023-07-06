<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
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
        $request = $this->validate([
            "email" => ["required", "string", "email"],
            "password" => ["required", "string"],
        ]);
        if ($request) {
            $user = Auth::authenticate($request);
            if ($user) {
                if (property_exists($request, "remember_me")) {
                    $remember_me = $request->remember_me;
                    if ($remember_me) {
                        Auth::rememberMe($user);
                    }
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
            }
        }
        return $this->sign_in();
    }

    #[Get("/admin/sign-in/2fa", "auth.sign_in_2fa")]
    public function sign_in_2fa(): string
    {
        $uuid = session()->get("two_fa_user");
        if (!$uuid) {
            app()->forbidden();
        }
        return twig("admin/auth/sign-in-2fa.html");
    }

    #[Post("/admin/sign-in/2fa", "auth.sign_in_2fa_post")]
    public function sign_in_2fa_post(): string
    {
        $uuid = session()->get("two_fa_user");
        $user = User::findByAttribute("uuid", $uuid);
        if (is_null($uuid) || is_null($user)) {
            app()->forbidden();
        }

        $request = $this->validate([
            "code" => [
                "2FA Code" => [
                    "numeric",
                    "required",
                    "min_length=6",
                    "max_length=6",
                ],
            ],
        ]);

        if ($request) {
            if (Auth::validateTwoFactorCode($user, $request->code)) {
                Auth::signIn($user);
            } else {
                Validate::addError("code", "Permission denied");
            }
        }
        return $this->sign_in_2fa();
    }
}
