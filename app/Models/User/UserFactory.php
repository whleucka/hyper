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
  public function create(string $name, string $email, string $password): ?User
  {
    $user = app()->get(User::class);
    $user->name = $name;
    $user->email = $email;
    $user->password = password_hash($password, PASSWORD_ARGON2I);
    return $user->save();
  }
}
