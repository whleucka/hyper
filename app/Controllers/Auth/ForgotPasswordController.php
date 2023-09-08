<?php

namespace App\Controllers\Auth;

use StellarRouter\{Get, Post};
use Nebula\Controller\Controller;

class ForgotPasswordController extends Controller
{
  #[Get("/forgot-password", "forgot-password.index")]
  public function index(): string
  {
    return latte("auth/forgot-password.latte");
  }

  #[Get("/forgot-password/part", "forgot-password.part")]
  public function index_part(): string
  {
    return latte("auth/forgot-password.latte", [], "body");
  }

  #[Post("/forgot-password", "forgot-password.post")]
  public function post(): string
  {
    if ($this->validate([
      "email" => ["required", "email"],
    ])) {
      // WIP
    }
    return $this->index_part();
  }
}