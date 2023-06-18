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

    /**
     * @param mixed $name
     * @param mixed $age
     */
    #[Get("/api/test/{name}/{age}", "home.test.name_age", ["api"])]
    public function name_age($name, $age): string {
        return "Name: $name, Age: $age";
    }
}
