<?php

namespace Nebula\Admin\Modules;

use Nebula\Admin\Module;

class Test extends Module
{
    public function __construct(private $id = null)
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
            "input" => "Input",
            "number" => "Number",
            "checkbox" => "Checkbox",
            "combo" => "Combo",
            "textarea" => "Text Area",
        ];

        $this->filters = [
            "name",
        ];

        $this->controls = [
            "name" => "input",
            "input" => "input",
            "number" => "number",
            "checkbox" => "checkbox",
            "combo" => "input",
            "textarea" => "textarea",
        ];

        $this->modify_validation = [
            "name" => ["required"],
            "input" => ["required"],
            "number" => ["required", "numeric"],
            "combo" => ["required"],
            "textarea" => ["required"],
        ];

        parent::__construct($id);
    }
}
