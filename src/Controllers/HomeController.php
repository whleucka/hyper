<?php

namespace Nebula\Controllers;

use StellarRouter\{Get, Post};

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): string
    {
        return "hello, world!";
    }

    #[Get("/view", "home.view")]
    public function view(): string
    {
        return twig("home/index.html", ["msg" => "hello, world!"]);
    }

    #[Get("/test/{echo}", "home.placeholder")]
    public function placeholder(string $echo): string {
        // The placeholder echo is derived from the URI
        // and it is accessible as a method parameter.
        return $echo;
    }

    #[Post("/api/test", "home.test", ["api"])]
    public function test(): int
    {
        // When you use the api middleware, 42 is returned
        // inside of a JSON response automatically.
        // There is no need to encode.
        return 42;
    }
}
