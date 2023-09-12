<?php

namespace App\Controllers\Auth;

use App\Models\User;
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
  public function index_part($show_success = false): string
  {
    return latte("auth/forgot-password.latte", [
      'show_message' => $show_success,
    ], "body");
  }

  #[Post("/forgot-password", "forgot-password.post")]
  public function post(): string
  {
    if ($this->validate([
      "email" => ["required", "email"],
    ])) {
      $user = User::search(['email' => request()->email]);
      if (!is_null($user)) {
        // If we have a user, then set the password reset token
        // Only set the token if the token doesn't exist or it is expired
        if (is_null($user->reset_token) || time() > $user->reset_expires_at) {
          // New token
          $token = token();
          // TODO move to config
          $expires = strtotime("+ 15 minute");
          $user->update([
            'reset_token' => $token,
            'reset_expires_at' => $expires,
          ]);
          $template = latte("auth/mail/forgot-password.latte", [
            'name' => $user->name,
            'link' => config("app.url") . "/password-reset/{$user->uuid}/{$token}/",
            'project' => config("app.name")
          ]);
          smtp()->send("Password reset", $template, to_addresses: [$user->email]);
        }
      }
      // Always display a message saying we sent the email
      return $this->index_part(true);
    }
    return $this->index_part();
  }
}
