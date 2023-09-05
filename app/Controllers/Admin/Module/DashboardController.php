<?php

namespace App\Controllers\Admin\Module;

use Nebula\Controller\ModuleController;
use StellarRouter\Get;
use StellarRouter\Group;

#[Group(prefix: "/admin", middleware: ["auth"])]
class DashboardController extends ModuleController
{
  #[Get("/module/dashboard", "dashboard.index")]
  public function index(): string
  {
    return latte("admin/dashboard.latte");
  }
}
