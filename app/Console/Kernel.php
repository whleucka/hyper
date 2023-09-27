<?php

namespace App\Console;

use App\Models\Music;
use Nebula\Console\Kernel as ConsoleKernel;
use App\Hyper\Music\Scan;

final class Kernel extends ConsoleKernel
{
    public function __construct()
    {
        // You can register new console commands like this
        // If you're not familiar with PHP getopt, you
        // can read more about it here:
        // https://www.php.net/manual/en/function.getopt.php
        $this->registerCommand(
            "short",
            "v",
            "Display version.",
            fn () => $this->version()
        );
        $this->registerCommand(
            "long",
            "version",
            "Display version.",
            fn () => $this->version()
        );

        $this->registerCommand(
            "long",
            "music-scan:",
            "Scan music directory.",
            fn ($directory) => $this->scanMusic($directory)
        );
        $this->registerCommand(
            "long",
            "music-metadata",
            "Update library metadata using getID3.",
            fn () => $this->musicMeta()
        );
    }

    protected function scanMusic(string $directory): void
    {
        $files = Scan::findFiles($directory);
        $count = 0;
        $music = new Music();

        db()->beginTransaction();
        foreach ($files as $path) {
            $count += $music->insert(["hash" => md5($path), "file_path" => $path], true) 
                ? 1 
                : 0;
        }
        db()->commit();

        $this->write("Scan complete");
        $this->write(sprintf("Music files: %s", count($files)));
        $this->write(sprintf("New files: %s", $count));
    }

    protected function musicMeta(): void
    {
        $music = Music::search(["NOT EXISTS (SELECT * FROM music_meta WHERE music_meta.music_id = music.id)"]);
        $count = 0;

        db()->beginTransaction();

        if (is_countable($music)) {
            // Loop around results and call setMetadata
            foreach ($music as $track) {
                $count += $track->setMetadata() 
                    ? 1 
                    : 0;
            }
        } else if ($music instanceof Music) {
            // Only one result
            $count = 1;
            $music->setMetadata();
        }
        
        db()->commit();

        $this->write("Update complete");
        $this->write(sprintf("Metadata updated: %s", $count));
    }

    protected function version(): void
    {
        $this->write("v0.0.1");
    }
}
