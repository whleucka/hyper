<?php

namespace Nebula\Controllers;

use StellarRouter\Get;

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): mixed
    {
        return "hello, world!";
    }
}
