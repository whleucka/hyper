<?php

namespace App\Models\User;

use Nebula\Model\Model;

class User extends Model
{
  protected string $table_name = "users";
  protected string $primary_key = "id";

  public function __construct(protected ?string $id = null)
  {
  }
}
