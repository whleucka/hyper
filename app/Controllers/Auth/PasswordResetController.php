<?php

namespace App\Controllers\Auth;

use App\Models\User;
use StellarRouter\{Get, Post};
use Nebula\Controller\Controller;

class PasswordResetController extends Controller
{
  #[Get("/password-reset/{uuid}/{token}", "password-reset.index")]
  public function index($uuid, $token): string
  {
    return latte("auth/password-reset.latte");
  }
}

