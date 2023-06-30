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
    public $remember_me;
    private $created_at;
    private $updated_at;

    public function __construct(?string $id = null)
    {
        parent::__construct("users", "id", $id);
    }
}
