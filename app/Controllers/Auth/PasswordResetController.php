<?php

namespace App\Controllers\Auth;

use App\Auth;
use App\Models\User;
use StellarRouter\{Get, Post};
use Nebula\Controller\Controller;
use Nebula\Traits\Http\Response as NebulaResponse;

final class PasswordResetController extends Controller
{
  use NebulaResponse;

  #[Get("/password-reset/{uuid}/{token}", "password-reset.index")]
  public function index($uuid, $token): string
  {
    $user = User::search(["uuid" => $uuid, "reset_token" => $token]);
    if ($user && time() < $user->reset_expires_at) {
      return latte("auth/password-reset.latte", [
        "uuid" => $uuid,
        "token" => $token,
      ]);
    }
    return $this->response(403, "Invalid token");
  }

  #[Get("/password-reset/{uuid}/{token}/part", "password-reset.part")]
  public function index_part($uuid, $token): string
  {
    $user = User::search(["uuid" => $uuid, "reset_token" => $token]);
    if ($user && time() < $user->reset_expires_at) {
      return latte("auth/password-reset.latte", [
        "uuid" => $uuid,
        "token" => $token,
      ], "body");
    }
    return $this->response(403, "Invalid token");
  }

  #[Post("/password-reset/{uuid}/{token}", "password-reset.post")]
  public function post($uuid, $token): string
  {
    $user = User::search(["uuid" => $uuid, "reset_token" => $token]);
    if (!$user) {
      return $this->response(403, "Invalid token");
    }
    if ($this->validate([
      "password" => [
        "required",
        "min_length=8",
        "uppercase=1",
        "lowercase=1",
        "symbol=1"
      ],
      "password_match" => ["Password" => ["required", "match"]]
    ])) {
      Auth::changePassword($user, request()->password);
      return Auth::signIn($user);
    }
    return $this->index_part($uuid, $token);
  }
}
