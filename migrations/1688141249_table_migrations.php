<?php
namespace Nebula\Migrations;

use Nebula\Interfaces\Database\Migration;
use Nebula\Database\Blueprint;
use Nebula\Database\Schema;

return new class implements Migration {
    public function up(): string
    {
        return Schema::create("migrations", function (Blueprint $table) {
            $table->id();
            $table->char("migration_hash", 32);
            $table->timestamp("ts")->default('NOW()');
            $table->primaryKey("id");
        });
    }

    public function down(): string
    {
        return Schema::drop("migrations");
    }
};
