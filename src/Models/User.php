<?php

namespace Nebula\Models;

class User extends Model
{
    public $uuid;
    public $name;
    public $email;
    public $password;
    public $two_fa_secret;
    public $remember_token;
    public $reset_token;
    public $reset_expires_at;
    public $failed_login_attempts;
    public $lock_expires_at;
    public $created_at;
    public $updated_at;
    public $gravatar;

    // These columns won't be updated on insert / update
    protected array $guarded = [
        'id',
        'uuid',
        'updated_at',
        'created_at',
    ];

    public function __construct(?string $id = null)
    {
        parent::__construct("users", "id", $id);
    }
}
