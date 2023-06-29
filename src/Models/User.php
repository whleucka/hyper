<?php

namespace Nebula\Models;

/**
  users schema

  CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password BINARY(40) NOT NULL,
    created_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(0) NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    UNIQUE KEY (email),
    PRIMARY KEY (id)
  );
*/

class User extends Model
{
    /**
     * User attributes are defined here
     * Only public properties can be inserted / updated
     */
    private $id;
    public $uuid;
    public $name;
    public $email;
    public $password;
    private $created_at;
    private $updated_at;

    public function __construct(?string $id = null)
    {
        parent::__construct("users", "id", $id);
    }
}
