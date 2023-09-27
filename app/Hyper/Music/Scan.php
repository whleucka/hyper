<?php

namespace App\Hyper\Music;

use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Scan
{
    private static array $formats = [
        "mp3",
        "ogg",
        "wav",
        "m4a",
        "aac",
        "flac",
        "alac",
        "pcm",
        "aiff",
    ];

    private static function isAudioFile(string $extension): bool
    {
        return in_array(strtolower($extension), self::$formats);
    }

    public static function findFiles(string $directory): array
    {
        if (!file_exists($directory)) {
            throw new Exception("Directory does not exist.");
        }
        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && self::isAudioFile($file->getExtension())) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
