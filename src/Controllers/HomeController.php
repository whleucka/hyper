<?php

namespace Nebula\Controllers;

use StellarRouter\Get;

class HomeController extends Controller
{
    #[Get("/")]
    public function index(): mixed
    {
        return "hello, world!";
    }
}
