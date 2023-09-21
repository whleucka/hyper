<?php

namespace App\Controllers;

use Nebula\Controller\ModuleController;
use StellarRouter\Get;
use StellarRouter\Group;

#[Group(middleware: ["auth"])]
class DashboardController extends ModuleController
{
    #[Get("/module/dashboard", "dashboard.index")]
    public function index(): string
    {
        return latte("dashboard/index.latte");
    }
}
