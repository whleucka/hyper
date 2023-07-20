<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;

class Users extends Module
{
    public function __construct(private $module_id = null)
    {
        $this->route = "users";
        $this->icon = "users";

        $this->table = [
            "id" => "ID",
            "uuid" => "UUID",
            "name" => "Name",
            "email" => "Email",
            "updated_at" => "Updated At",
            "created_at" => "Created At",
        ];

        $this->form = [
            "name" => "Name",
            "email" => "Email",
            "'' as password" => "Password",
            "'' as password_match" => "Password (again)",
        ];

        parent::__construct($module_id);
    }
}