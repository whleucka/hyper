<?php

namespace Nebula\Controllers\Admin;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
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
        return twig("admin/sign-in.html");
    }

    #[Get("/admin/register", "auth.register")]
    public function register(): string
    {
        return twig("admin/register.html");
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
}
