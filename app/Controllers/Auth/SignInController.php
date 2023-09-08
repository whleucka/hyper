<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Nebula\Controller\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post};

final class SignInController extends Controller
{
  #[Get("/sign-in", "sign-in.index")]
  public function index(): string
  {
    return latte("auth/sign-in.latte");
  }

  #[Get("/sign-in/part", "sign-in.part")]
  public function index_part(): string
  {
    return latte("auth/sign-in.latte", [
      'email' => request()->get("email"),
    ], "body");
  }

  #[Post("/sign-in", "sign-in.post", ["rate_limit"])]
  public function post(): string
  {
    if ($this->validate([
      "email" => ["required", "email"],
      "password" => ["required"],
    ])) {
      $user = User::search(['email' => request()->email]);
      if ($user && password_verify(request()->password, $user->password)) {
        // Set the user session
        session()->set("user", $user->uuid);
        // Redirect to the dashboard
        return redirectRoute("dashboard.index");
      } else {
        // Trigger some errors
        Validate::addError("password", "Bad email or password");
      }
    }
    // Validation failed, show the sign in form
    return $this->index_part();
  }
}
