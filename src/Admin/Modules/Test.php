<?php

namespace Nebula\Admin\Modules;

use Nebula\Admin\Module;

class Test extends Module
{
    public function __construct(private $id = null)
    {
        $this->route = "test";
        $this->parent = "Debug";

        $this->column("id", "ID")
            ->column("name", "Name")
            ->column("updated_at", "Update At")
            ->column("created_at", "Created At");
        
        $this->search("name");

        $this->control("name", "Name")
            ->control("input", "Input")
            ->control("number", "Number", "number")
            ->control("checkbox", "Checkbox", "checkbox")
            ->control("textarea", "Textarea", "textarea");

        $this->rule("name", ["required"])
            ->rule("input", ["required"])
            ->rule("number", ["required", "numeric"])
            ->rule("combo", ["required"])
            ->rule("textarea", ["required"]);

        parent::__construct($id);
    }
}
