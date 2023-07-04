<?php

namespace Nebula\Controllers\Admin;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Models\User;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class AuthController extends Controller
{
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

    #[Get("/admin/register", "auth.register")]
    public function register(): string
    {
        return twig("admin/auth/register.html");
    }

    #[Get("/admin/forgot-password", "auth.forgot_password")]
    public function forgot_password($email_sent = false): string
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
                Auth::signIn($user);
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
            "password" => [
                "required",
                "string",
                "min_length=8",
                "uppercase=1",
                "lowercase=1",
                "symbol=1",
            ],
            // We don't want the validation message to say "Password_check"
            // so we can override the label like this. Now the validation
            // message will say "Password" for the field
            "password_check" => ["Password" => ["required", "match"]],
        ]);
        if ($request) {
            $user = Auth::register($request);
            if ($user) {
                Auth::signIn($user);
            }
        }
        return $this->register();
    }

    #[Post("/admin/forgot-password", "auth.forgot_password_post")]
    public function forgot_password_post()
    {
        $request = $this->validate([
            "email" => ["required", "email"],
        ]);
        if ($request) {
            Auth::forgotPassword($request->email);
        }
        return $this->forgot_password(true);
    }

    #[Post("/admin/password-reset/{uuid}/{token}", "auth.password_reset_post")]
    public function password_reset_post(string $uuid, string $token): string
    {
        $user = User::findByAttribute("uuid", $uuid);
        if (!Auth::validateForgotPassword($user, $token)) {
            app()->forbidden();
        }
        $request = $this->validate([
            "password" => [
                "required",
                "string",
                "min_length=8",
                "uppercase=1",
                "lowercase=1",
                "symbol=1",
            ],
            "password_check" => ["Password" => ["required", "match"]],
        ]);
        if ($request) {
            if (Auth::changePassword($user, $request->password)) {
                Auth::signIn($user);
            }
        }
        return $this->password_reset($uuid, $token);
    }
}
