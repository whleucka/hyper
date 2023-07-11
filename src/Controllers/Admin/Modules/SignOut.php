<?php

namespace Nebula\Controllers\Admin\Modules;

class SignOut extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "sign-out",
            "title" => "Sign out",
            "parent" => "System",
        ];
        parent::__construct($config);
    }
}
