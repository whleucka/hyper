<?php

namespace App\Models;

use Nebula\Model\Model;
use App\Hyper\Music\Metadata;

final class Music extends Model
{
    public string $table_name = "music";
    public string $primary_key = "id";

    public function __construct(protected ?string $id = null)
    {
    }

    public function meta()
    {
        return MusicMeta::search(["music_id", $this->id]);
    }

    public function setMetadata(): mixed
    {
        $metadata = Metadata::analyzeTags($this->file_path); 
        extract($metadata);
        $data = [
            "music_id" => $this->id,
            "artist" => $comments_html["artist"][0] ?? "No artist",
            "album" => $comments_html["album"][0] ?? "No album",
            "title" => $comments_html["title"][0] ?? "No title",
            "cover" => "/assets/img/no-album.png",
            "track_number" => $comments_html["track_number"][0] ?? "",
            "playtime_string" => $playtime_string ?? "",
            "playtime_seconds" => $playtime_seconds ?? "",
            "bitrate" => $bitrate ?? "",
            "mime_type" => $mime_type ?? "",
            "genre" => implode(", ", $comments_html["genre"] ?? []),
            "year" => $comments_html["year"][0] ?? "",
        ];
        $music_meta = new MusicMeta;
        return $music_meta->insert($data);
    }

    public function play(): void
    {
        ob_start();
        // TODO: transcode to mp3
        $file = $this->file_path;
        ob_clean();
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: binary");
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Accept-Ranges: bytes');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit();
    }
}
