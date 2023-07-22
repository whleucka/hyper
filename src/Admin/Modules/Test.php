<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;

class Test extends Module
{
    public function __construct(private $module_id = null)
    {
        $this->route = "test";
        $this->parent = "Debug";

        $this->table = [
            "id" => "ID",
            "name" => "Name",
            "updated_at" => "Updated At",
            "created_at" => "Created At",
        ];

        $this->form = [
            "name" => "Name",
            "number" => "Number",
            "input" => "Input",
            "checkbox" => "Checkbox",
            "combo" => "Combo",
            "textarea" => "Text Area",
        ];

        $this->controls = [
            "name" => "input",
            "number" => "number",
            "input" => "input",
            "checkbox" => "input",
            "combo" => "input",
            "textarea" => "textarea",
        ];

        $this->modify_validation = [
            "name" => ["required"],
            "number" => ["required", "numeric"],
            "input" => ["required"],
            "checkbox" => ["required"],
            "combo" => ["required"],
            "textarea" => ["required"],
        ];

        parent::__construct($module_id);
    }
}
