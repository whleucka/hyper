<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Profile extends Module
{
    public function __construct()
    {
        $config = [
            "route" => "profile",
            "title" => "Profile",
            "icon" => "user",
            "parent" => "Administration",
        ];
        parent::__construct($config);
    }

    protected function data(): array
    {
        return [
            "content" => twig("admin/profile/index.html"),
        ];
    }
}
