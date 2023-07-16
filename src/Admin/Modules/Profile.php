<?php

namespace Nebula\Admin\Modules;
use Nebula\Admin\Module;

class Profile extends Module
{
    public function __construct()
    {
        $this->create_enabled = $this->destroy_enabled = $this->edit_enabled = false;
        $this->route = "profile";
        $this->icon = "user";
        parent::__construct();
    }

    protected function view(): string
    {
        return twig("admin/profile/index.html");
    }
}
