<?php

namespace App\Controllers\Admin\Auth;

use App\Models\User;
use Nebula\Controller\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post, Group};

#[Group(prefix: "/admin")]
final class SignInController extends Controller
{
  #[Get("/sign-in", "sign-in.index")]
  public function index(): string
  {
    return twig("admin/auth/sign-in.html", [
      'email' => request()->get("email"),
    ]);
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
        Validate::addError("email");
        Validate::addError("password", "Bad email or password");
      }
    }
    // Validation failed, show the sign in form
    return $this->index();
  }
}
