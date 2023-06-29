<?php

namespace Nebula\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Nebula\Models\User;
use stdClass;

class Auth
{
  public static function signOut(): void
  {
    $_SESSION = [];
    session_destroy();
    // TODO lookup route
    $route = "/admin/sign-in";
    $response = new RedirectResponse($route); 
    $response->send();
  }
  public static function register(stdClass $data): ?User
  {
    $user = new User;
    $user->uuid = uuid();
    $user->name = $data->name;
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_ARGON2I);
    return $user->insert();
  }

  public static function signIn(User $user): void
  {
    // TODO lookup route
    $route = "/admin";
    $_SESSION['user'] = $user->id;
    $response = new RedirectResponse($route); 
    $response->send();
  }
}
