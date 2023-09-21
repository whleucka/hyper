<?php

namespace App\Controllers\Auth;

use App\Auth;
use App\Models\User;
use Nebula\Controller\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

final class SignInController extends Controller
{
    #[Get("/sign-in", "sign-in.index")]
    public function index(): string
    {
        return latte("auth/sign-in.latte", [
            "two_fa_enabled" => config("auth.two_fa_enabled"),
        ]);
    }

    #[Get("/sign-in/part", "sign-in.part")]
    public function index_part(): string
    {
        return latte(
            "auth/sign-in.latte",
            [
                "two_fa_enabled" => config("auth.two_fa_enabled"),
                "email" => request()->get("email"),
            ],
            "body"
        );
    }

    #[Post("/sign-in", "sign-in.post", ["rate_limit"])]
    public function post(): string
    {
        if (
            $this->validate([
                "email" => ["required", "email"],
                "password" => ["required"],
            ])
        ) {
            $user = User::search(["email" => request()->email]);
            if ($user && Auth::validatePassword($user, request()->password)) {
                if (config("auth.two_fa_enabled")) {
                    return Auth::twoFactorAuthentication($user);
                } else {
                    return Auth::signIn($user);
                }
            } else {
                Validate::addError("password", "Bad email or password");
            }
        }
        return $this->index_part();
    }
}
