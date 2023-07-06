<?php

namespace Nebula\Controllers\Admin;

use Nebula\Controllers\Controller;

use StellarRouter\Get;

class AdminController extends Controller
{
    #[Get("/admin", "admin.index", ["auth"])]
    public function index(): string
    {
        return twig("admin/index.html");
    }

    #[Get("/admin/profile", "admin.profile", ["auth"])]
    public function profile(): string
    {
        return twig("admin/profile.html");
    }
}
