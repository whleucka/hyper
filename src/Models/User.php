<?php

namespace Nebula\Models;

class User extends Model
{
    /**
     * User attributes are defined here
     * Only public properties can be inserted / updated
     */
    private $id;
    private $uuid;
    public $name;
    public $email;
    public $password;
    public $two_fa_secret;
    public $remember_token;
    public $reset_token;
    public $reset_expires_at;
    public $failed_login_attempts;
    public $lock_expires_at;
    private $created_at;
    private $updated_at;
    // The attributes below are not part of the entity
    public $gravatar;

    public function __construct(?string $id = null)
    {
        parent::__construct("users", "id", $id);
    }
}
