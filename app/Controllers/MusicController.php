<?php

namespace App\Controllers;

use App\Models\Music;
use Nebula\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(middleware: ["auth"])]
class MusicController extends Controller
{
    #[Get("/music/play/{hash}", "music.play")]
    public function play(string $hash): void
    {
        $track = Music::search(["hash", $hash]);
        if ($track) {
            $track->play();
        }
    }

    #[Get("/music/info/{hash}", "music.info", ["api"])]
    public function info(string $hash): array
    {
        $track = Music::search(["hash", $hash]);
        if ($track) {
            $metadata = $track->meta();
            if ($metadata) {
                return [
                    'artist' => $metadata->artist,
                    'album' => $metadata->album,
                    'title' => $metadata->title,
                    'cover' => $metadata->cover
                ];
            }
        }
        return [];
    }
}

