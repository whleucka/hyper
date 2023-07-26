<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\Get;

class WelcomeController extends Controller
{
  #[Get("/", "welcome.index")]
  public function index()
  {
    echo "hello, world!";
  }
}
