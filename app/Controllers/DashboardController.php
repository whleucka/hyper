<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(middleware: ["auth"])]
class DashboardController extends Controller
{
    #[Get("/dashboard", "dashboard.index")]
    public function index(): string
    {
        return latte("dashboard/index.latte");
    }
}
