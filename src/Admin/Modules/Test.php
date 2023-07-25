<?php

namespace Nebula\Admin\Modules;

use Nebula\Admin\Module;

class Test extends Module
{
    public function __construct(private $id = null)
    {
        $this->route = "test";
        $this->parent = "Debug";

        // Table columns
        $this->column("id", "ID")
            ->column("name", "Name")
            ->column("updated_at", "Update At")
            ->column("created_at", "Created At");
        
        // Filters
        $this->search("name");

        // Form controls
        $this->control("name", "Name")
            ->control("input", "Input")
            ->control("number", "Number", "number")
            ->control("checkbox", "Checkbox", "checkbox")
            ->control("combo", "Select", fn($column, $title, $value) => "<p>$column: select not yet implemented</p>")
            ->control("textarea", "Textarea", "textarea");

        // Validation
        $this->rule("name", ["required"])
            ->rule("input", ["required"])
            ->rule("number", ["required", "numeric"])
            ->rule("textarea", ["required"]);

        parent::__construct($id);
    }
}
