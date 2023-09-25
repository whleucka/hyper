<?php

namespace App\Controllers;

use Nebula\Controller\ModuleController as NebulaModule;
use StellarRouter\{Get, Group};

#[Group(middleware: ["auth"])]
class ModuleController extends NebulaModule
{
    #[Get("/dashboard", "dashboard.index")]
    public function index(): string
    {
        return latte("dashboard/index.latte");
    }
}
