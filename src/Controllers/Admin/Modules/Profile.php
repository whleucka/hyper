<?php

namespace Nebula\Controllers\Admin\Modules;

class Profile extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "profile",
            "title" => "Profile",
            "parent" => "Administration"
        ];
        parent::__construct($config);
    }
}
