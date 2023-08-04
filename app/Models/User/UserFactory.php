<?php

namespace App\Models\User;

use Nebula\Database\Factory;

class UserFactory extends Factory
{
  /**
   * Create a new user model
   * @param string $name
   * @param string $email
   * @param string $password
   * @return User|null
   */
  function create(string $name, string $email, string $password): ?User
  {
    return User::create([
      "name" => $name,
      "email" => $email,
      "password" => password_hash($password, PASSWORD_ARGON2I),
    ]);
  }
}
