<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;
use Nebula\Controllers\Admin\Auth\RegisterController;

class Users extends Module
{
    public function __construct(private $id = null)
    {
        $this->route = "users";
        $this->icon = "users";

        // Table columns
        $this->column("id", "ID")
            ->column("uuid", "UUID")
            ->column("name", "Name")
            ->column("email", "Email")
            ->column("updated_at", "Update At")
            ->column("created_at", "Created At");

        // Form controls
        $this->control("name", "Name")
            ->control("'' as password", "Password", "password")
            ->control("'' as password_match", "Password (again)", "password");

        // Share the same validation rules as register controller
        $password_rules = RegisterController::$password_rules;
        $this->rule("password", $password_rules['password'])
            ->rule("password_match", ["Password" => ['required', 'match']]);


        // Filters
        $this->search([
            "name",
            "email"
        ]);

        parent::__construct($id);
    }
}
