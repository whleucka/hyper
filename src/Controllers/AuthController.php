<?php

namespace Nebula\Controllers;

use StellarRouter\{Get,Post};

class AuthController extends Controller
{
    #[Get("/admin/sign-in", "auth.sign_in")]
    public function sign_in(): string
    {
        return twig("auth/sign-in.html");
    }

    #[Get("/admin/register", "auth.register")]
    public function register(): string
    {
        return twig("auth/register.html");
    }

    #[Post("/admin/sign-in", "auth.sign_in_post")]
    public function sign_in_post(): string
    {
        dump($this->request);
        return $this->sign_in();
    }

    #[Post("/admin/register", "auth.register_post")]
    public function register_post(): string
    {
        dump($this->request);
        return $this->register();
    }
}

