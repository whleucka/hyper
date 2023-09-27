<?php

namespace App\Controllers;

use App\Models\Music;
use Nebula\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(middleware: ["auth"])]
class PlaylistController extends Controller
{
    private function getPlaylist(): array
    {
        $playlist = session()->get("music_playlist") ?? [];
        $tracks = [];
        foreach ($playlist as $hash) {
            $sql = "SELECT * FROM music 
                INNER JOIN music_meta ON music_id = music.id 
                WHERE hash = ?
                ORDER BY artist, album";
            $result = db()->select($sql, $hash);
            if ($result) {
                $tracks[] = $result;
            }
        }
        return $tracks;
    }

    #[Get("/playlist", "playlist.index")]
    public function index(): string
    {
        return latte("playlist/index.latte", ["playlist" => $this->getPlaylist()]);
    }

    #[Get("/playlist/part", "playlist.part", ["push-url=/playlist"])]
    public function part(): string
    {
        return latte("playlist/index.latte", ["playlist" => $this->getPlaylist()], "content");
    }

    #[Get("/playlist/get", "playlist.get", ["api"])]
    function get(): mixed
    {
        $playlist = session()->get("music_playlist") ?? [];
        return $playlist;
    }


    #[Get("/playlist/add/{hash}", "playlist.add", ["api"])]
    function add(string $hash): mixed
    {
        $playlist = session()->get("music_playlist") ?? [];
        $track = Music::search(["hash", $hash]);
        if ($track) {
            $playlist[] = $hash;
            session()->set("music_playlist", $playlist);
        }
        return $playlist;
    }

    #[Get("/playlist/remove/{hash}", "playlist.remove", ["api", "push-url=/playlist"])]
    function remove(string $hash): mixed
    {
        $playlist = session()->get("music_playlist") ?? [];
        $track = Music::search(["hash", $hash]);
        if ($track) {
            $playlist = array_filter($playlist, fn($target) => $target != $hash);
            session()->set("music_playlist", array_values($playlist));
        }
        return $playlist;
    }
}

