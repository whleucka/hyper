<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\Get;

class DashboardController extends Controller
{
    #[Get("/dashboard", "dashboard.index", ["push-url"])]
    public function index(): string
    {
        return latte("dashboard/index.latte");
    }
}
