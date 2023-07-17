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
            "textarea" => "Text Area",
            "checkbox" => "Checkbox",
            "combo" => "Combo",
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
