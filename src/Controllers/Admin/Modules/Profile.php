<?php

namespace Nebula\Controllers\Admin\Modules;
use Nebula\Controllers\Admin\Module;

class Profile extends Module
{
    public function __construct()
    {
        $this->route = "profile";
        $this->title = "Profile";
        $this->icon = "user";
        $this->parent = "Administration";
        parent::__construct();
    }

    protected function getContent(): array
    {
        return [
            "content" => twig("admin/profile/index.html"),
        ];
    }
}
