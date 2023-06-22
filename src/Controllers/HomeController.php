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

    #[Get("/template", "home.template")]
    public function template(): string
    {
        return $this->render("home/index.html", ["msg" => "hello, world!"]);
    }

    #[Get("/placeholder/{echo}", "home.placeholder")]
    public function placeholder(string $echo): string
    {
        // The placeholder echo is derived from the URI 
        // and it is accessible as a method parameter.
        return $echo;
    }

    #[Get("/api/json", "home.json", ["api"])]
    public function json(): int
    {
        // When you use the api middleware, 42 is returned 
        // inside of a JSON response automatically. 
        // There is no need to encode.
        return 42;
    }
}
