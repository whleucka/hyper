<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\Get;

final class WelcomeController extends Controller
{
  #[Get("/", "welcome.index", ["cached"])]
  public function index(): string
  {
    // This will render the view app/Views/welcome/index.html
    // and return the rendered HTML as a string
    // This response will be cached using redis for 15 minutes
    return twig("welcome/index.html");
  }
}
