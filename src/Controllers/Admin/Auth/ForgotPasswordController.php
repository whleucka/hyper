<?php

namespace Nebula\Controllers\Admin\Auth;

use Nebula\Admin\Auth;
use Nebula\Controllers\Controller;
use StellarRouter\{Get, Post};

class ForgotPasswordController extends Controller
{
    #[Get("/admin/forgot-password", "auth.forgot_password")]
    public function forgot_password(bool $email_sent = false): string
    {
        return twig("admin/auth/forgot-password.html", [
            "email_sent" => $email_sent,
        ]);
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
}
