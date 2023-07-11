<?php

namespace Nebula\Controllers\Admin\Modules;

class Users extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "users",
            "title" => "Users",
            "parent" => "Administration",
        ];
        parent::__construct($config);
    }
}
