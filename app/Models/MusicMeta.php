<?php

namespace App\Models;

use Nebula\Model\Model;

final class MusicMeta extends Model
{
    public string $table_name = "music_meta";
    public string $primary_key = "id";

    public function __construct(protected ?string $id = null)
    {
    }
}

