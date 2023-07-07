<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

class RegisterController extends Controller
{
    public static $password_rules = [
        "password" => [
            "required",
            "string",
            "min_length=8",
            "uppercase=1",
            "lowercase=1",
            "symbol=1",
        ],
    ];

    #[Get("/admin/register", "auth.register")]
    public function register(): string
    {
        return twig("admin/auth/register.html");
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
            ...self::$password_rules,
        ]);
        if ($request) {
            $user = Auth::register($request);
            if ($user) {
                // Generate 2FA secret for user
                Auth::twoFactorSecret($user);
                if (Auth::twoFactorEnabled()) {
                    session()->set("two_fa_user", $user->uuid);
                    app()->redirect("auth.register_2fa");
                } else {
                    Auth::signIn($user);
                }
            }
        }
        return $this->register();
    }
}
