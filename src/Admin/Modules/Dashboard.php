<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;

class Dashboard extends Module
{
    public function __construct()
    {
        $this->create_enabled = $this->destroy_enabled = $this->modify_enabled = false;
        $this->route = "dashboard";
        $this->icon = "star";
        parent::__construct();
    }

    protected function view(): string
    {
        return twig("admin/dashboard/index.html");
    }
}
