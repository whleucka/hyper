<?php

namespace App\Models\Factories;

use Nebula\Model\Factory;
use App\Models\User;

class UserFactory extends Factory
{
  protected string $model_class = User::class;

  /**
   * Create a new user model
   * @param string $name
   * @param string $email
   * @param string $password
   * @return User|null
   */
  public function create(string $name, string $email, string $password): ?User
  {
    $user = app()->get($this->model_class);
    $user->name = $name;
    $user->email = $email;
    $user->password = password_hash($password, PASSWORD_ARGON2I);
    return $user->save();
  }
}
