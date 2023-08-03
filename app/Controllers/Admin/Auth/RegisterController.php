<?php

namespace App\Controllers\Admin\Auth;

use App\Models\User;
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
  public function post(): string
  {
    if ($this->validate([
      "name" => ["required"],
      "email" => ["required", "unique=users", "email"],
      "password" => [
        "required",
        "min_length=8",
        "uppercase=1",
        "lowercase=1",
        "symbol=1"
      ],
      // Note: you can change the label so that it
      // doesn't say Password_match in the UI
      "password_match" => ["Password" => ["required", "match"]]
    ])) {
      $user = User::create([
        "name" => request()->name,
        "email" => request()->email,
        "password" => password_hash(request()->password, PASSWORD_ARGON2I),
      ]);
      if ($user) {
        // Set the user session
        session()->set("user", $user->uuid);
        // Redirect to the dashboard
        return redirectRoute("dashboard.index");
      }
    } else {
      dd($this->errors);
    }
    // Validation failed, show the register form
    return $this->index();
  }
}
