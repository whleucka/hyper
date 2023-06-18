<?php

namespace Nebula\Controllers;

use StellarRouter\Get;

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): string
    {
        return "hello, world!";
    }

    #[Get("/api/test", "home.api.test", ["api"])]
    public function test(): int
    {
        return 42;
    }
}
