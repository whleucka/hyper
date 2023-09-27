<?php

namespace Nebula\Migrations;

use Nebula\Interfaces\Database\Migration;
use Nebula\Database\Blueprint;
use Nebula\Database\Schema;

return new class () implements Migration 
{
    public function up(): string
    {
      return Schema::create("music", function (Blueprint $table) {
        $table->id();
        $table->char("hash", 32);
        $table->text("file_path")->nullable();
        $table->dateTime("created_at")->default("CURRENT_TIMESTAMP");
        $table->unique("hash");
        $table->primaryKey("id");
      });
    }

    public function down(): string
    {
      return Schema::drop("music");
    }
};
