<?php

namespace App\Controllers\Admin\Module;

use Nebula\Controller\ModuleController;
use StellarRouter\{Get, Group};

#[Group(prefix: "/admin")]
class Dashboard extends ModuleController
{
  public function __construct()
  {
  }

  #[Get("/module/dashboard", "dashboard.index")]
  public function index(): string
  {
    return twig("admin/dashboard.html", []);
  }
}
