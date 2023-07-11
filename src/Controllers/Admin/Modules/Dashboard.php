<?php

namespace Nebula\Controllers\Admin\Modules;

class Dashboard extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "dashboard",
            "title" => "Dashboard",
            "parent" => "Administration",
        ];
        parent::__construct($config);
    }

    protected function data(): array
    {
        return [
            "content" => twig("admin/dashboard/index.html"),
        ];
    }
}
