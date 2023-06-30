<?php
namespace Nebula\Migrations;

use Nebula\Database\Blueprint;
use Nebula\Database\Migration;
use Nebula\Database\Schema;

return new class extends Migration {
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
