<?php

namespace App\Controllers\Auth;

use App\Auth;
use App\Models\User;
use StellarRouter\{Get, Post};
use Nebula\Controller\Controller;

final class ForgotPasswordController extends Controller
{
    #[Get("/forgot-password", "forgot-password.index")]
    public function index(): string
    {
        return latte("auth/forgot-password.latte");
    }

    #[Get("/forgot-password/part", "forgot-password.part")]
    public function index_part($show_success = false): string
    {
        return latte(
            "auth/forgot-password.latte",
            [
                "show_success_message" => $show_success,
            ],
            "body"
        );
    }

    #[Post("/forgot-password", "forgot-password.post")]
    public function post(): string
    {
        if (
            $this->validate([
                "email" => ["required", "email"],
            ])
        ) {
            $user = User::search(["email" => request()->email]);
            Auth::forgotPassword($user);
            // Always display a success message
            return $this->index_part(true);
        }
        return $this->index_part();
    }
}
