<?php

namespace App\Controllers\Admin\Auth;

use App\Models\Factories\UserFactory;
use Nebula\Controller\Controller;
use Nebula\Validation\Validate;
use StellarRouter\{Get, Post, Group};

#[Group(prefix: "/admin")]
final class RegisterController extends Controller
{
  #[Get("/register", "register.index")]
  public function index(): string
  {
    return twig("admin/auth/register.html", []);
  }

  #[Post("/register", "register.post")]
  public function post(): string
  {
    // Provide a custom message for the unique rule
    Validate::$messages["unique"] = "An account already exists for this email address";
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
      $factory = app()->get(UserFactory::class);
      $user = $factory->create(
        request()->name,
        request()->email,
        request()->password
      );
      if ($user) {
        // Set the user session
        session()->set("user", $user->uuid);
        // Redirect to the dashboard
        return redirectRoute("dashboard.index");
      }
    } 
    // Validation failed, show the register form
    return $this->index();
  }
}
