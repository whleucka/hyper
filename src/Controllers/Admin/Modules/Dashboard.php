<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Dashboard extends Module
{
    public function __construct()
    {
        $this->create_enabled = $this->destroy_enabled = $this->edit_enabled = false;
        $this->route = "dashboard";
        $this->title = "Dashboard";
        $this->icon = "star";
        $this->parent = "Administration";
        parent::__construct();
    }

    protected function view(): string
    {
        return twig("admin/dashboard/index.html");
    }
}
