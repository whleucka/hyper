<?php

namespace Nebula\Controllers\Home;

use StellarRouter\Get;
use Nebula\Controllers\Controller;

class HomeController extends Controller
{
    #[Get("/", "home.index")]
    public function index(): string
    {
        // A simple response
        return "hello, world!";
    }

    #[Get("/view", "home.view")]
    public function view(): string
    {
        // To render a twig template, just simply call the twig helper function
        return twig("home/index.html", ["msg" => "hello, world!"]);
    }

    #[Get("/echo/{echo}", "home.echo")]
    public function echo(string $echo): string
    {
        // The placeholder echo is derived from the URI
        // and it is accessible as a method parameter.
        return $echo;
    }

    #[Get("/api/test", "home.test", ["api"])]
    public function test(): int
    {
        // When you use the api middleware, 42 is returned
        // inside of a JSON response automatically.
        return 42;
    }
}
