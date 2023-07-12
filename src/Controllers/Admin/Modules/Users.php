<?php

namespace Nebula\Controllers\Admin\Modules;

class Users extends Module
{
    public function __construct()
    {
        $config = [
            "table" => "users",
            "route" => "users",
            "title" => "Users",
            "parent" => "Administration",
        ];

        $this->table = [
            "ID" => "id",
            "UUID" => "uuid",
            "Name" => "name",
            "Email" => "email",
            "Updated At" => "updated_at",
            "Created At" => "created_at",
        ];

        $this->form = [
            "Name" => "name",
            "Email" => "email",
            "Password" => "password",
            "Password (again)" => "password_match",
        ];

        parent::__construct($config);
    }

}
