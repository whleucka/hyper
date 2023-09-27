<?php

namespace App\Hyper\Music;

use getID3;

class Metadata
{
    public static function analyzeTags(string $file_path): ?array
    {
        $getID3 = new getID3;
        $result = $getID3->analyze($file_path);
        $getID3->CopyTagsToComments($result);
        return $result;
    } 
}
