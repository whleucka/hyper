<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Dashboard extends Module
{
    public function __construct()
    {
        $this->route = "dashboard";
        $this->title = "Dashboard";
        $this->icon = "star";
        $this->parent = "Administration";
        parent::__construct();
    }

    protected function getContent(): array
    {
        return [
            "content" => twig("admin/dashboard/index.html"),
        ];
    }
}
