<?php

namespace App\Controllers\Admin\Auth;

use Nebula\Controller\Controller;
use StellarRouter\{Get, Post, Group};

#[Group(prefix: "/admin")]
class RegisterController extends Controller
{
  #[Get("/register", "register.index")]
  public function index(): string
  {
    return twig("admin/auth/register.html", []);
  }

  #[Post("/register", "register.post")]
  public function post()
  {
    dump(request());
    die("wip");
  }
}

