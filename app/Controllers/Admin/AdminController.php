<?php

namespace App\Controllers\Admin;

use Nebula\Controller\ModuleController;
use StellarRouter\Get;

class AdminController extends ModuleController
{
  #[Get("/admin", "admin.index")]
  public function index(): mixed
  {
    // Redirect to the dashboard if the user is logged in
    return user() ? redirectRoute('dashboard.index') : redirectRoute('sign-in.index');
  }
}
