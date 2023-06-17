<?php

namespace Nebula\Controllers;
use StellarRouter\Get;

class HomeController
{
    #[Get("/")]
    public function index(): mixed
    {
        return "hello, world!";
    }
}
