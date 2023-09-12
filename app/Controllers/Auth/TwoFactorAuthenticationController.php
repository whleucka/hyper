<?php

namespace App\Controllers\Auth;

use Nebula\Controller\Controller;
use StellarRouter\{Get, Post};

final class TwoFactorAuthenticationController extends Controller
{
  #[Get("/two-factor-authentication", "two-factor-authentication.index")]
  public function index(): string
  {
    return latte("auth/two-factor-authentication.latte");
  }

  #[Get("/two-factor-authentication/part", "two-factor-authentication.part")]
  public function index_part(): string
  {
    return latte("auth/two-factor-authentication.latte", block: "body");
  }

  #[Post("/two-factor-authentication", "two-factor-authentication.post")]
  public function post(): string
  {
    if ($this->validate([
      "code" => ["required", "numeric", "min_length=6", "max_length=6"],
    ])) {
      die("wip");
    } 
    return $this->index_part();
  }
}
