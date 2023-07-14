<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Users extends Module
{
    public function __construct(private $module_id = null)
    {
        $config = [
            "table" => "users",
            "primary_key" => "id",
            "route" => "users",
            "title" => "Users",
            "icon" => "users",
            "parent" => "Administration",
            "create_enabled" => true,
            "destroy_enabled" => true,
            "edit_enabled" => true,
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
            "Password" => "'' as password",
            "Password (again)" => "'' as password_match",
        ];

        parent::__construct($config, $module_id);
    }
}
