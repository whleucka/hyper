<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\Get;

final class WelcomeController extends Controller
{
  #[Get("/", "welcome.index")]
  public function index()
  {
    return "hello, world!";
  }
}
