<?php

namespace Nebula\Controllers\Admin\Modules;

use Error;

class Users extends Module
{
    public function __construct()
    {
        $config = [
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

    protected function data(): array
    {
        $route = app()->getRoute();
        return match ($route->getName()) {
            "module.index" => ["content" => twig("layouts/table.html", [
                "columns" => array_keys($this->table),
                "data" => $this->tableData('users'),
            ])],
            "module.edit" => ["content" => twig("layouts/form.html")],
            default => throw new Error("module name doesn't exist")
        };
    }
}
