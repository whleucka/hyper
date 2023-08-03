<?php

namespace App\Models\User;

class Factory
{
  public static function create(string $name, string $email, string $password): User
  {
    return User::create([
      "name" => $name,
      "email" => $email,
      "password" => password_hash($password, PASSWORD_ARGON2I),
    ]);
  }
}
