<?php

namespace App\Controllers\Admin\Module;

use Nebula\Controller\ModuleController;
use StellarRouter\Get;
use StellarRouter\Group;

#[Group(prefix: "/admin", middleware: ["auth"])]
class Dashboard extends ModuleController
{
  #[Get("/module/dashboard", "dashboard.index")]
  public function index(): string
  {
    return twig("admin/dashboard.html", []);
  }
}
