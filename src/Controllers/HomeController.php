<?php

namespace Nebula\Controllers;

use StellarRouter\Get;

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): mixed
    {
        throw new \Exception("test");
        return "hello, world!";
    }
}
