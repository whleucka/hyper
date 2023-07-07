<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class SignIn2FAController extends Controller
{
    #[Get("/admin/sign-in/2fa", "auth.sign_in_2fa")]
    public function sign_in_2fa(): string
    {
        $uuid = session()->get("two_fa_user");
        if (is_null($uuid)) {
            app()->forbidden();
        }

        return twig("admin/auth/sign-in-2fa.html");
    }

    #[Post("/admin/sign-in/2fa", "auth.sign_in_2fa_post")]
    public function sign_in_2fa_post(): mixed
    {
        if (
            !$this->validate([
                "code" => [
                    "2FA Code" => [
                        "numeric",
                        "required",
                        "min_length=6",
                        "max_length=6",
                    ],
                ],
            ])
        ) {
            return $this->sign_in_2fa();
        }

        $user = User::findByAttribute("uuid", session()->get("two_fa_user"));
        if (is_null($user)) {
            app()->forbidden();
        }

        if (Auth::validateTwoFactorCode($user, request()->get("code"))) {
            Auth::signIn($user);
        } else {
            Validate::addError("code", "Permission denied");
            return $this->sign_in_2fa();
        }
    }
}
