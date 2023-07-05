<?php

namespace Nebula\Controllers\Admin;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class AuthController extends Controller
{
    private $password_rules = [
        "password" => [
            "required",
            "string",
            "min_length=8",
            "uppercase=1",
            "lowercase=1",
            "symbol=1",
        ],
    ];

    #[Get("/admin/sign-out", "auth.sign_out")]
    public function sign_out(): void
    {
        Auth::signOut();
    }

    #[Get("/admin/sign-in", "auth.sign_in")]
    public function sign_in(): string
    {
        return twig("admin/auth/sign-in.html");
    }

    #[Get("/admin/sign-in/2fa", "auth.sign_in_2fa")]
    public function two_factor(): string
    {
        $uuid = session()->get("two_fa_user");
        if (!$uuid) {
            app()->forbidden();
        }
        return twig("admin/auth/two-factor.html");
    }

    #[Get("/admin/register", "auth.register")]
    public function register(): string
    {
        return twig("admin/auth/register.html");
    }

    #[Get("/admin/register/2fa", "auth.register_2fa")]
    public function two_factor_qr(): string
    {
        $uuid = session()->get("two_fa_user");
        if (!$uuid) {
            app()->forbidden();
        }

        $user = User::findByAttribute("uuid", $uuid);
        $continue = $this->request->get("continue");
        if ($continue) {
            Auth::signIn($user);
        }

        return twig("admin/auth/two-factor-qr.html", [
            "qr_url" => Auth::getQR($user),
        ]);
    }


    #[Get("/admin/forgot-password", "auth.forgot_password")]
    public function forgot_password(bool $email_sent = false): string
    {
        return twig("admin/auth/forgot-password.html", [
            "email_sent" => $email_sent,
        ]);
    }

    #[Get("/admin/password-reset/{uuid}/{token}", "auth.password_reset")]
    public function password_reset(string $uuid, string $token): string
    {
        $user = User::findByAttribute("uuid", $uuid);
        if (!Auth::validateForgotPassword($user, $token)) {
            app()->forbidden();
        }
        return twig("admin/auth/password-reset.html");
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
                } else{
                    Auth::signIn($user);
                }
            } else {
                // Add a custom validation error for bad password
                Validate::addError("password", "Oops! Bad email or password");
                // We can trigger an error with no message. In this case,
                // the email field will turn red with no message.
                Validate::addError("email");
            }
        }
        return $this->sign_in();
    }

    #[Post("/admin/register", "auth.register_post")]
    public function register_post(): string
    {
        // Override the unique message
        Validate::$messages["unique"] =
            "An account already exists for this email address";
        $request = $this->validate([
            "name" => ["required", "string"],
            "email" => ["required", "string", "email", "unique=users"],
            // We don't want the validation message to say "Password_check"
            // so we can override the label like this. Now the validation
            // message will say "Password" for the field
            "password_check" => ["Password" => ["required", "match"]],
            ...$this->password_rules,
        ]);
        if ($request) {
            $user = Auth::register($request);
            if ($user) {
                if (Auth::twoFactorEnabled()) {
                    Auth::twoFactorSecret($user);
                    session()->set("two_fa_user", $user->uuid);
                    app()->redirect("auth.register_2fa");
                } else{
                    Auth::signIn($user);
                }
            }
        }
        return $this->register();
    }

    #[Post("/admin/forgot-password", "auth.forgot_password_post")]
    public function forgot_password_post(): string
    {
        $request = $this->validate([
            "email" => ["required", "email"],
        ]);
        if ($request) {
            Auth::forgotPassword($request->email);
            return $this->forgot_password(true);
        }
        return $this->forgot_password();
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
                "Password" => [
                    "required",
                    "match",
                ]
            ],
            ...$this->password_rules,
        ]);
        if ($request) {
            if (Auth::changePassword($user, $request->password)) {
                Auth::signIn($user);
            }
        }
        return $this->password_reset($uuid, $token);
    }

    #[Post("/admin/sign-in/2fa", "auth.sign_in_2fa_post")]
    public function two_factor_post(): string
    {
        $uuid = session()->get("two_fa_user");
        if (!$uuid) {
            app()->forbidden();
        }

        $request = $this->validate([
            "code" => [
                "2FA Code" => [
                    "numeric",
                    "required",
                    "min_length=6",
                    "max_length=6",
                ]
            ]
        ]);

        $user = User::findByAttribute("uuid", $uuid);
        if ($request) {
            if (Auth::validateTwoFactorCode($user, $request->code)) {
                Auth::signIn($user);
            } else {
                Validate::addError("code", "Permission denied");
            }
        }
        return $this->two_factor();
    }
}
