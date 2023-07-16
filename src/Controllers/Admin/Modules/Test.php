<?php

namespace Nebula\Controllers\Admin\Modules;

use Nebula\Controllers\Admin\Module;

class Test extends Module
{
    public function __construct(private $module_id = null)
    {
        $this->create_enabled = $this->destroy_enabled = $this->edit_enabled = true;
        $this->route = "test";
        $this->title = "Test";
        $this->parent = "Debug";
        $this->table_name = "test";

        $this->table = [
            "ID" => "id",
            "Name" => "name",
            "Updated At" => "updated_at",
            "Created At" => "created_at",
        ];

        $this->form = [
            "Name" => "name",
            "Number" => "number",
            "Input" => "input",
            "Text Area" => "textarea",
            "Checkbox" => "checkbox",
            "Combo" => "combo",
        ];

        $this->modify_validation = [
            "name" => ["required"],
            "number" => ["required"],
            "checkbox" => ["required", "numeric"],
            "combo" => ["required", "numeric"],
        ];

        parent::__construct($module_id);
    }
}
