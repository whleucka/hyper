<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class Register2FAController extends Controller
{
    #[Get("/admin/register/2fa", "auth.register_2fa")]
    public function register_2fa(): string
    {
        $uuid = session()->get("two_fa_user");
        if (is_null($uuid)) {
            app()->forbidden();
        }

        $user = User::findByAttribute("uuid", $uuid);
        $continue = request()->get("continue");
        if ($continue) {
            Auth::signIn($user);
        }

        return twig("admin/auth/register-2fa.html", [
            "qr_url" => Auth::getQR($user),
        ]);
    }

    #[Post("/admin/register/2fa", "auth.register_2fa_post")]
    public function register_2fa_post(): string
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
            return $this->register_2fa();
        }

        $user = User::findByAttribute("uuid", session()->get("two_fa_user"));
        if (is_null($user)) {
            app()->forbidden();
        }

        if (Auth::validateTwoFactorCode($user, request()->get("code"))) {
            Auth::signIn($user);
        } else {
            Validate::addError("code", "Permission denied");
            return $this->register_2fa();
        }
    }
}
