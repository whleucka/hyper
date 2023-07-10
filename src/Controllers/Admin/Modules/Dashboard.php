<?php

namespace Nebula\Controllers\Admin\Modules;

class Dashboard extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "dashboard",
            "title" => "Dashboard",
            "icon" => "home"
        ];
        parent::__construct($config);
    }
}
