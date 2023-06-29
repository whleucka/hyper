<?php

namespace Nebula\Admin;

use Nebula\Models\User;
use stdClass;

/**
 * Planned static methods:
 * register
 * login
 */
class Auth
{
  public static function register(stdClass $data): ?User
  {
    $user = new User;
    $user->uuid = uuid();
    $user->name = $data->name;
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_ARGON2I);
    return $user->insert();
  }

  public static function login()
  {
  }

  public static function signIn(User $user)
  {
    dump($user);
    die("success");
  }
}
