<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Users extends Module
{
    public function __construct(private $module_id = null)
    {
        $this->create_enabled = $this->destroy_enabled = $this->edit_enabled = true;
        $this->table_name = "users";
        $this->route = "users";
        $this->title = "Users";
        $this->icon = "users";
        $this->parent = "Administration";

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

        parent::__construct($module_id);
    }
}
