<?php

namespace Nebula\Controllers\Admin\Modules;

class Profile extends Module
{
    public function __construct()
    {
        parent::__construct("profile", "Profile", "user");
    }
}
