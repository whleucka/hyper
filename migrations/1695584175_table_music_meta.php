<?php

namespace Nebula\Migrations;

use Nebula\Interfaces\Database\Migration;
use Nebula\Database\Blueprint;
use Nebula\Database\Schema;

return new class () implements Migration 
{
    public function up(): string
    {
      return Schema::create("music_meta", function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger("music_id")->nullable();
        $table->varchar("artist");
        $table->text("album");
        $table->text("title");
        $table->varchar("cover");
        $table->varchar("track_number");
        $table->varchar("playtime_seconds");
        $table->varchar("playtime_string");
        $table->varchar("bitrate");
        $table->varchar("mime_type");
        $table->varchar("genre");
        $table->varchar("year");
        $table->timestamps();
        $table->primaryKey("id");
        $table->foreignKey("music_id")
          ->references("music", "id")
          ->onDelete("CASCADE");
      });
    }

    public function down(): string
    {
      return Schema::drop("music_meta");
    }
};

