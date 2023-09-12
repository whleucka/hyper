<?php

namespace App;

class Auth
{
  public static function hashPassword(string $password): string
  {
    return password_hash($password, PASSWORD_ARGON2I);
  }
}
