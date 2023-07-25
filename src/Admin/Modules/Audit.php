<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;

class Audit extends Module
{
    public function __construct(private $id = null)
    {
        $this->route = "audit";
        $this->parent = "Administration";
        $this->icon = "check-circle";
        $this->edit_enabled = $this->destroy_enabled = $this->create_enabled = false;

        $this->search([
            "table_name", 
            "table_id", 
            "field"
        ]);

        $this->column("id", "ID")
            ->column("(SELECT name FROM users WHERE id = user_id) as user", "Audit User")
            ->column("table_name", "Table")
            ->column("table_id", "Table ID")
            ->column("field", "Field")
            ->column("old_value", "Old Value")
            ->column("new_value", "New Value")
            ->column("message", "Message")
            ->column("created_at", "Created At");

        parent::__construct($id);
    }
}

