<?php

namespace App\Controllers\Admin\Auth;

use Nebula\Controller\Controller;
use StellarRouter\{Get, Post, Group};

#[Group(prefix: "/admin")]
class SignInController extends Controller
{
  #[Get("/sign-in", "sign-in.index")]
  public function index(): string
  {
    return twig("admin/auth/sign-in.html", []);
  }

  #[Post("/sign-in", "sign-in.post")]
  public function post()
  {
    dump(request());
    die("wip");
  }
}
