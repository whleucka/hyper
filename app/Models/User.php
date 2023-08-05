<?php

namespace App\Models;

use Nebula\Model\Model;

final class User extends Model
{
  public string $table_name = "users";
  public string $primary_key = "id";

  // Columns won't be inserted/updated
  protected array $guarded = [
    "id",
    "uuid",
    "two_fa_secret",
    "remember_token",
    "reset_token",
    "reset_expires_at",
    "failed_login_attempts",
    "lock_expires_at",
    "created_at",
    "updated_at",
  ];

  public function __construct(protected ?string $id = null)
  {
  }
}
