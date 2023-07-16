<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Admin\Module;

class Users extends Module
{
    public function __construct(private $module_id = null)
    {
        $this->route = "users";
        $this->icon = "users";

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
