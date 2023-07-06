<?php

namespace Nebula\Models;

class User extends Model
{
    /**
     * User attributes are defined here
     * Only public & protected properties can be modified
     * Private properties are immutable
     */
    private $id;
    private $uuid;
    protected $name;
    protected $email;
    protected $password;
    protected $two_fa_secret;
    protected $remember_token;
    protected $reset_token;
    protected $reset_expires_at;
    protected $failed_login_attempts;
    protected $lock_expires_at;
    private $created_at;
    private $updated_at;

    // The properties below are not entity attributes,
    // so they won't be affected by insert/update etc.
    public $gravatar;

    public function __construct(?string $id = null)
    {
        parent::__construct("users", "id", $id);
    }
}
