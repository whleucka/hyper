<?php

namespace Nebula\Controllers;

use StellarRouter\Get;

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): string
    {
        return $this->render("home/index.html", ["msg" => "hello, world!"]);
    }

    #[Get("/api/test", "home.api.test", ["api"])]
    public function test(): int
    {
        throw new \Error('test');
        return 42;
    }
}
