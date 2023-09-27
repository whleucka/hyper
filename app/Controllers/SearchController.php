<?php

namespace App\Controllers;

use Nebula\Controller\Controller;
use StellarRouter\{Get, Group, Post};

#[Group(middleware: ["auth"])]
class SearchController extends Controller
{
    #[Get("/search", "search.index")]
    public function index(): string
    {
        $search = session()->get("music_search");
        return latte("search/index.latte", ["search_value" => $search]);
    }

    #[Get("/search/part", "search.part", ["push-url=/search"])]
    public function part(): string
    {
        $search = session()->get("music_search");
        return latte("search/index.latte", ["search_value" => $search], "content");
    }

    #[Post("/search", "search.post")]
    public function post(): mixed
    {
        if (
            $this->validate([
                "search" => ["required"],
            ])
        ) {
            $search = request()->search;
            session()->set("music_search", $search);
            $sql = "SELECT * FROM music 
                INNER JOIN music_meta ON music_id = music.id 
                WHERE (artist LIKE ?) OR 
                (album LIKE ?) OR 
                (title LIKE ?)
                ORDER BY artist, album 
                LIMIT 1000";
            $results = db()->selectAll($sql, ...array_fill(0, 3, "%$search%"));
            return latte("search/results.latte", ["results" => $results]);
        }
        session()->set("music_search", "");
        return null;
    }
}
